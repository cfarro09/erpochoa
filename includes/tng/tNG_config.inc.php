<?php
// Array definitions
  $tNG_login_config = array();
  $tNG_login_config_session = array();
  $tNG_login_config_redirect_success  = array();
  $tNG_login_config_redirect_failed  = array();

// Start Variable definitions
  $tNG_debug_mode = "DEVELOPMENT";
  $tNG_debug_log_type = "";
  $tNG_debug_email_to = "you@yoursite.com";
  $tNG_debug_email_subject = "[BUG] The site went down";
  $tNG_debug_email_from = "webserver@yoursite.com";
  $tNG_email_host = "";
  $tNG_email_user = "";
  $tNG_email_port = "25";
  $tNG_email_password = "";
  $tNG_email_defaultFrom = "nobody@nobody.com";
  $tNG_login_config["connection"] = "Ventas";
  $tNG_login_config["table"] = "acceso";
  $tNG_login_config["pk_field"] = "codacceso";
  $tNG_login_config["pk_type"] = "NUMERIC_TYPE";
  $tNG_login_config["email_field"] = "";
  $tNG_login_config["user_field"] = "usuario";
  $tNG_login_config["password_field"] = "clave";
  $tNG_login_config["level_field"] = "nivel";
  $tNG_login_config["level_type"] = "STRING_TYPE";
  $tNG_login_config["randomkey_field"] = "";
  $tNG_login_config["activation_field"] = "";
  $tNG_login_config["password_encrypt"] = "true";
  $tNG_login_config["autologin_expires"] = "30";
  $tNG_login_config["redirect_failed"] = "ventas/index.php";
  $tNG_login_config["redirect_success"] = "principal01.php";
  $tNG_login_config["login_page"] = "index.php";
  $tNG_login_config["max_tries"] = "";
  $tNG_login_config["max_tries_field"] = "";
  $tNG_login_config["max_tries_disableinterval"] = "";
  $tNG_login_config["max_tries_disabledate_field"] = "";
  $tNG_login_config["registration_date_field"] = "";
  $tNG_login_config["expiration_interval_field"] = "";
  $tNG_login_config["expiration_interval_default"] = "";
  $tNG_login_config["logger_pk"] = "codhistacceso";
  $tNG_login_config["logger_table"] = "acceso_historial";
  $tNG_login_config["logger_user_id"] = "codacceso";
  $tNG_login_config["logger_ip"] = "ip";
  $tNG_login_config["logger_datein"] = "ultimo_login";
  $tNG_login_config["logger_datelastactivity"] = "ultima_actividad";
  $tNG_login_config["logger_session"] = "sesion";
  $tNG_login_config_session["kt_login_id"] = "codacceso";
  $tNG_login_config_session["kt_login_user"] = "usuario";
  $tNG_login_config_session["kt_login_level"] = "nivel";
  $tNG_login_config_session["kt_clave"] = "clave";
  $tNG_login_config_session["kt_codigopersonal"] = "codigopersonal";
  $tNG_login_config_session["kt_estado"] = "estado";
// End Variable definitions
?>