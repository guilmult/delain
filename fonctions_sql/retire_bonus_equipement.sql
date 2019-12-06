--
-- Name: retire_bonus_equipement(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function retire_bonus_equipement(integer, integer) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Rajoute un bonus d'équipement à un perso
-- $1 = Le code du perso en question
-- $2 = le code de l'objet qui donne ce bonus

declare
  v_perso alias for $1;
  v_obj_cod alias for $2;

  ligne record;
  code_retour text;
begin

  -- supression des bonus normaux
  delete from bonus where bonus_perso_cod=v_perso and bonus_obj_cod=v_obj_cod ;

  -- supression des bonus de carac
  update carac_orig set corig_nb_tours=0 where corig_perso_cod=v_perso and corig_obj_cod=v_obj_cod ;

  -- remise des caracs en état après la suppression
  perform f_remise_caracs(v_perso) ;

  return 0;
end;$_$;


ALTER FUNCTION public.retire_bonus_equipement(integer, integer) OWNER TO delain;
