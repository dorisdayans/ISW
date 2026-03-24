<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(<<<'SQL'
        CREATE OR REPLACE FUNCTION sync_course_seats_on_enrollment()
        RETURNS trigger
        LANGUAGE plpgsql
        AS $$
        BEGIN

            -- Caso 1: nueva matrícula activa
        IF TG_OP = 'INSERT' THEN
            UPDATE courses
            SET available_seats = available_seats - 1,
                enrolled_count  = enrolled_count + 1
            WHERE id = NEW.course_id
            AND deleted_at IS NULL
            AND available_seats > 0;

            IF NOT FOUND THEN
                RAISE EXCEPTION 'No hay cupos disponibles para el curso %', NEW.course_id;
            END IF;

            RETURN NEW;
        END IF;

                -- Caso 2: soft delete de matrícula
        IF TG_OP = 'UPDATE'
        AND OLD.deleted_at IS NULL
        AND NEW.deleted_at IS NOT NULL THEN

            UPDATE courses
            SET available_seats = available_seats + 1,
                enrolled_count  = GREATEST(enrolled_count - 1, 0)
            WHERE id = OLD.course_id
            AND deleted_at IS NULL;

            RETURN NEW;
        END IF;


        -- Caso 3: restore de matrícula
        IF TG_OP = 'UPDATE'
        AND OLD.deleted_at IS NOT NULL
        AND NEW.deleted_at IS NULL THEN

            UPDATE courses
            SET available_seats = available_seats - 1,
                enrolled_count  = enrolled_count + 1
            WHERE id = NEW.course_id
            AND deleted_at IS NULL
            AND available_seats > 0;

            IF NOT FOUND THEN
                RAISE EXCEPTION 'No hay cupos disponibles para restaurar la matrícula del curso %', NEW.course_id;
            END IF;

            RETURN NEW;
        END IF;

        RETURN NEW;
    END;
    $$;
    SQL);

        DB::statement(<<<'SQL'
        CREATE TRIGGER trg_sync_course_seats_on_enrollment
        AFTER INSERT OR UPDATE OF deleted_at
        ON enrollments
        FOR EACH ROW
        EXECUTE FUNCTION sync_course_seats_on_enrollment();
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            DROP TRIGGER IF EXISTS trg_sync_course_seats_on_enrollment ON enrollments
        ");

        DB::statement("
            DROP FUNCTION IF EXISTS sync_course_seats_on_enrollment()
        ");
    }
};
