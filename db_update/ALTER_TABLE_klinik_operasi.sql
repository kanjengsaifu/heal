ALTER TABLE "klinik"."klinik_operasi" ADD COLUMN "id_anes_jenis" Character Varying( 255 ) COLLATE "pg_catalog"."default";COMMENT ON COLUMN "klinik"."klinik_operasi"."id_anes_jenis" IS 'nyimpen anes_jenis_id dari tabel klinik.klinik_anes_jenis';
ALTER TABLE "klinik"."klinik_operasi" ADD COLUMN "id_op_jenis" Bigint;COMMENT ON COLUMN "klinik"."klinik_operasi"."id_op_jenis" IS 'nyimpen jenis operasi dari klinik_operasi_metode';
ALTER TABLE "klinik"."klinik_operasi" DROP CONSTRAINT IF EXISTS "klinik_operasi_fk";
ALTER TABLE "klinik"."klinik_operasi" DROP COLUMN "id_op_metode";
ALTER TABLE "klinik"."klinik_operasi" ADD COLUMN "id_op_metode" Character Varying( 255 ) COLLATE "pg_catalog"."default";