<?php
/**
 * ==============================================================================
 * PARKAPP - SISTEMA DE GESTIÓN Y RESERVA DE PARQUEOS EN TIEMPO REAL
 * Front Controller (Punto de entrada único)
 * ==============================================================================
 */

// Carga de configuración global
require_once __DIR__ . '/app/config/database.php';

// Carga de clases del núcleo
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/Auth.php';

// Iniciar manejo de sesiones
Auth::start();

// Inicializar enrutador
$router = new Router();

// ==========================================
// RUTAS DE AUTENTICACIÓN Y PÚBLICAS
// ==========================================
$router->get('', 'ConductorController@disponibilidad');
$router->get('login', 'AuthController@login');
$router->post('login', 'AuthController@authenticate');
$router->get('registro', 'AuthController@registro');
$router->post('registro', 'AuthController@register');
$router->get('logout', 'AuthController@logout');

// ==========================================
// SECCIÓN 1: PORTAL DEL CONDUCTOR
// ==========================================
$router->get('disponibilidad', 'ConductorController@disponibilidad');
$router->get('reservar', 'ConductorController@reservar');
$router->get('mis-reservas', 'ConductorController@misReservas');
$router->get('reserva/qr', 'ConductorController@verQR');

// ==========================================
// SECCIÓN 2: MÓDULO DE CASETA / OPERADOR
// ==========================================
$router->get('caseta', 'OperadorController@caseta');
$router->get('caseta/ingreso', 'OperadorController@ingreso');
$router->get('caseta/salida', 'OperadorController@salidaCobro');
$router->get('caseta/ticket', 'OperadorController@ticketPrint');

// ==========================================
// SECCIÓN 3: PANEL ADMINISTRATIVO Y REPORTES
// ==========================================
$router->get('admin/dashboard', 'AdminController@dashboard');
$router->get('admin/parqueos', 'AdminController@parqueos');
$router->get('admin/espacios', 'AdminController@espacios');
$router->get('admin/tarifas', 'AdminController@tarifas');
$router->get('admin/usuarios', 'AdminController@usuarios');
$router->get('reportes/ingresos', 'ReporteController@ingresos');
$router->get('reportes/ocupacion', 'ReporteController@ocupacion');

// Despachar la petición entrante
$router->dispatch();
