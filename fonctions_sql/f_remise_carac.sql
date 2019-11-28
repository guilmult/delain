--
-- Name: f_remise_carac(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_remise_carac() RETURNS text
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction f_remise_caracs                      */
/*  permet de remettre les caracs à l’origine    */
/*************************************************/
declare
	code_retour text;
	ligne record;
	v_pv integer;
	v_con_actu integer;
	v_diff_pv integer;
	temp_tue text;
begin
	for ligne in
		select corig_perso_cod, corig_type_carac, corig_carac_valeur_orig
		from carac_orig
		where (corig_dfin < now() or corig_nb_tours = 0)
	loop
		if ligne.corig_type_carac = 'FOR' then
			-- on attaque la force, et le poids transporté
			update perso
			set perso_for = ligne.corig_carac_valeur_orig,
				perso_enc_max = (ligne.corig_carac_valeur_orig * 3)
			where perso_cod = ligne.corig_perso_cod;
		elsif ligne.corig_type_carac = 'DEX' then
			-- dexterite
			update perso
			set perso_dex = ligne.corig_carac_valeur_orig
			where perso_cod = ligne.corig_perso_cod;
		elsif ligne.corig_type_carac = 'CON' then
			-- constitution
			select into v_pv,v_con_actu perso_pv,perso_con
			from perso
			where perso_cod = ligne.corig_perso_cod;

			v_diff_pv = (ligne.corig_carac_valeur_orig - v_con_actu) * 3;
			update perso
			set perso_pv = perso_pv + v_diff_pv,
				perso_pv_max = perso_pv_max + v_diff_pv,
				perso_con = ligne.corig_carac_valeur_orig
			where perso_cod = ligne.corig_perso_cod;

			select into v_pv perso_pv
			from perso
			where perso_cod = ligne.corig_perso_cod;

			if v_pv <= 0 then
				temp_tue := 'Un bonus de constitution a pris fin. La perte des PV temporaires vous a été fatale.';
				perform insere_evenement(ligne.corig_perso_cod, ligne.corig_perso_cod, 10, temp_tue, 'N', NULL);
				temp_tue := tue_perso_final(ligne.corig_perso_cod, ligne.corig_perso_cod);
			end if;
		elsif ligne.corig_type_carac = 'INT' then
			update perso
			set perso_int = ligne.corig_carac_valeur_orig
			where perso_cod = ligne.corig_perso_cod;
		end if;
		delete from carac_orig
		where corig_perso_cod = ligne.corig_perso_cod
			and corig_type_carac = ligne.corig_type_carac;
	end loop;
	return 'OK';
end;$$;


ALTER FUNCTION public.f_remise_carac() OWNER TO delain;

--
-- Name: FUNCTION f_remise_carac(); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_remise_carac() IS 'Annule tous les bonus / malus de caractéristiques arrivés à terme.';


--
-- Name: f_remise_caracs(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_remise_caracs(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************/
/* fonction f_remise_caracs                      */
/*  permet de remettre les caracs à l’origine    */
/*  Cette version fonctionne pour un perso précis*/
/* $1 = perso_cod                                */
/*************************************************/
declare
	v_perso alias for $1;
	code_retour text;
	ligne record;
	v_pv integer;
	v_con_actu integer;
	v_diff_pv integer;
	temp_tue text;
begin
	for ligne in
		select corig_perso_cod, corig_type_carac, corig_carac_valeur_orig
		from carac_orig
		where corig_perso_cod = v_perso and
			(corig_dfin < now() or corig_nb_tours = 0)
	loop
		if ligne.corig_type_carac = 'FOR' then
			-- on attaque la force, et le poids transporté
			update perso
			set perso_for = ligne.corig_carac_valeur_orig,
				perso_enc_max = (ligne.corig_carac_valeur_orig * 3)
			where perso_cod = ligne.corig_perso_cod;
		elsif ligne.corig_type_carac = 'DEX' then
			-- dexterite
			update perso
			set perso_dex = ligne.corig_carac_valeur_orig
			where perso_cod = ligne.corig_perso_cod;
		elsif ligne.corig_type_carac = 'CON' then
			-- constitution
			select into v_pv,v_con_actu perso_pv,perso_con
			from perso
			where perso_cod = ligne.corig_perso_cod;

			v_diff_pv = (ligne.corig_carac_valeur_orig - v_con_actu) * 3;
			update perso
			set perso_pv = perso_pv + v_diff_pv,
				perso_pv_max = perso_pv_max + v_diff_pv,
				perso_con = ligne.corig_carac_valeur_orig
			where perso_cod = ligne.corig_perso_cod;

			select into v_pv perso_pv
			from perso
			where perso_cod = ligne.corig_perso_cod;

			if v_pv <= 0 then
				temp_tue := 'Un bonus de constitution a pris fin. La perte des PV temporaires vous a été fatale.';
				perform insere_evenement(ligne.corig_perso_cod, ligne.corig_perso_cod, 10, temp_tue, 'N', NULL);
				temp_tue := tue_perso_final(ligne.corig_perso_cod, ligne.corig_perso_cod);
			end if;
		elsif ligne.corig_type_carac = 'INT' then
			update perso
			set perso_int = ligne.corig_carac_valeur_orig
			where perso_cod = ligne.corig_perso_cod;
		end if;
		delete from carac_orig
		where corig_perso_cod = ligne.corig_perso_cod
			and corig_type_carac = ligne.corig_type_carac;
	end loop;
	return 'OK';
end;$_$;


ALTER FUNCTION public.f_remise_caracs(integer) OWNER TO delain;

--
-- Name: FUNCTION f_remise_caracs(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_remise_caracs(integer) IS 'Remets les caractéristiques d’origine suite aux malus qui se terminent, pour le perso $1.';

