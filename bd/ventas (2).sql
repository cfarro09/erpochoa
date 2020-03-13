-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-09-2016 a las 15:11:19
-- Versión del servidor: 5.6.21
-- Versión de PHP: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `ventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso`
--

CREATE TABLE IF NOT EXISTS `acceso` (
`codacceso` int(11) NOT NULL,
  `usuario` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `clave` varchar(32) COLLATE utf8_spanish2_ci NOT NULL,
  `nivel` enum('root','admin','user') COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'user',
  `codigopersonal` int(11) NOT NULL,
  `estado` int(1) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `acceso`
--

INSERT INTO `acceso` (`codacceso`, `usuario`, `clave`, `nivel`, `codigopersonal`, `estado`) VALUES
(1, 'hackherman', 'a1a6907c989946085b0e35493786fce3', 'root', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso_historial`
--

CREATE TABLE IF NOT EXISTS `acceso_historial` (
`codhistacceso` int(11) NOT NULL,
  `codacceso` int(11) NOT NULL,
  `ip` char(15) COLLATE utf8_spanish2_ci NOT NULL,
  `ultimo_login` date NOT NULL,
  `ultima_actividad` date NOT NULL,
  `sesion` varchar(200) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `acceso_historial`
--

INSERT INTO `acceso_historial` (`codhistacceso`, `codacceso`, `ip`, `ultimo_login`, `ultima_actividad`, `sesion`) VALUES
(1, 1, '::1', '2016-08-01', '2016-08-01', 'esgrj5mtkehvjkt6thsv5m6r95'),
(2, 1, '::1', '2016-08-03', '2016-08-03', 'esgrj5mtkehvjkt6thsv5m6r95'),
(3, 1, '::1', '2016-08-05', '2016-08-05', 'esgrj5mtkehvjkt6thsv5m6r95'),
(4, 1, '::1', '2016-08-05', '2016-08-05', 'esgrj5mtkehvjkt6thsv5m6r95'),
(5, 1, '::1', '2016-08-05', '2016-08-05', 'esgrj5mtkehvjkt6thsv5m6r95'),
(6, 1, '::1', '2016-08-05', '2016-08-05', 'esgrj5mtkehvjkt6thsv5m6r95'),
(7, 1, '::1', '2016-08-07', '2016-08-30', 'esgrj5mtkehvjkt6thsv5m6r95'),
(8, 1, '::1', '2016-09-03', '2016-09-03', 'esgrj5mtkehvjkt6thsv5m6r95'),
(9, 1, '::1', '2016-09-03', '2016-09-03', 'esgrj5mtkehvjkt6thsv5m6r95'),
(10, 1, '::1', '2016-09-03', '2016-09-03', 'esgrj5mtkehvjkt6thsv5m6r95'),
(11, 1, '::1', '2016-09-03', '2016-09-03', 'r7r5nagtjru6ks0ujkip6b5kq3'),
(12, 1, '::1', '2016-09-04', '2016-09-04', 'esgrj5mtkehvjkt6thsv5m6r95'),
(13, 1, '::1', '2016-09-05', '2016-09-05', 'r7r5nagtjru6ks0ujkip6b5kq3'),
(14, 1, '::1', '2016-09-05', '2016-09-05', 'r7r5nagtjru6ks0ujkip6b5kq3'),
(15, 1, '::1', '2016-09-05', '2016-09-05', 'r7r5nagtjru6ks0ujkip6b5kq3'),
(16, 1, '::1', '2016-09-05', '2016-09-05', 'r7r5nagtjru6ks0ujkip6b5kq3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banco`
--

CREATE TABLE IF NOT EXISTS `banco` (
`codigobanco` int(11) NOT NULL,
  `nombre_banco` varchar(50) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caracteristica_equipo`
--

CREATE TABLE IF NOT EXISTS `caracteristica_equipo` (
`codcaracprod` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `codigoprod` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE IF NOT EXISTS `cargos` (
`codcar` int(11) NOT NULL,
  `nombre_cargo` varchar(100) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo_personal`
--

CREATE TABLE IF NOT EXISTS `cargo_personal` (
`codcarper` int(11) NOT NULL,
  `codigopersonal` int(11) NOT NULL,
  `codcar` int(11) NOT NULL,
  `fecha_actualizar` date NOT NULL,
  `observacion` text CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cargo_personal`
--

INSERT INTO `cargo_personal` (`codcarper`, `codigopersonal`, `codcar`, `fecha_actualizar`, `observacion`, `estado`) VALUES
(1, 2, 1, '2016-08-11', '.', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE IF NOT EXISTS `categoria` (
`codigocat` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`codigocat`, `nombre`, `estado`) VALUES
(1, 'MOUSE', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cjuridico`
--

CREATE TABLE IF NOT EXISTS `cjuridico` (
`codigoclientej` int(11) NOT NULL,
  `ruc` varchar(13) NOT NULL,
  `razonsocial` varchar(100) NOT NULL,
  `fax` char(10) NOT NULL,
  `telefono` char(10) NOT NULL,
  `celular` char(14) NOT NULL,
  `contacto` varchar(60) NOT NULL,
  `email` varchar(120) NOT NULL,
  `fecha_ingreso` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cnatural`
--

CREATE TABLE IF NOT EXISTS `cnatural` (
`codigoclienten` int(11) NOT NULL,
  `cedula` varchar(11) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `paterno` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `materno` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `ciudad` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `celular` varchar(14) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `direccion` varchar(80) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `sexo` enum('M','F') NOT NULL,
  `fecha_ingreso` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `color`
--

CREATE TABLE IF NOT EXISTS `color` (
`codigocolor` int(11) NOT NULL,
  `nombre_color` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante_cpmpra`
--

CREATE TABLE IF NOT EXISTS `comprobante_cpmpra` (
  `codigocomprobante` int(11) NOT NULL,
  `numero` varchar(25) NOT NULL,
  `fecha_emision` varchar(12) NOT NULL,
  `fecha_registro` varchar(12) NOT NULL,
  `nombre_vendedor` varchar(25) NOT NULL,
  `orden_compra` varchar(25) NOT NULL,
  `sub_total` float NOT NULL,
  `igv` float NOT NULL,
  `total` float NOT NULL,
  `nombre_recepcionap` varchar(25) NOT NULL,
  `observacion` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE IF NOT EXISTS `configuracion` (
`codconf` int(11) unsigned zerofill NOT NULL,
  `nombresistema` varchar(200) COLLATE utf8_spanish2_ci NOT NULL,
  `logo` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `favicon` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `footer` varchar(200) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`codconf`, `nombresistema`, `logo`, `favicon`, `footer`) VALUES
(00000000001, 'Sistema de Administración y Gestion de Local de Ventas y Servicios de Computo', 'img/logo.png', 'img/logo.png', '2020 © Todos los derechos reservados');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE IF NOT EXISTS `detalle_ventas` (
`codigodetalleproducto` int(11) unsigned zerofill NOT NULL,
  `codigo` char(20) COLLATE utf8_spanish2_ci NOT NULL,
  `codigoprod` int(11) unsigned zerofill NOT NULL,
  `cantidad` int(6) DEFAULT '1',
  `pventa` float NOT NULL,
  `concatenacion` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `codcomprobante` varchar(25) COLLATE utf8_spanish2_ci DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_computo`
--

CREATE TABLE IF NOT EXISTS `equipo_computo` (
`codigoequipo` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `codigoclientej` int(11) NOT NULL,
  `codigoclienten` int(11) NOT NULL,
  `tipoequipo` varchar(25) NOT NULL,
  `caracteristicas` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_mov_invbien`
--

CREATE TABLE IF NOT EXISTS `historial_mov_invbien` (
`codigo_hmib` int(11) unsigned zerofill NOT NULL,
  `codigo` char(20) COLLATE utf8_spanish2_ci NOT NULL,
  `codigotipomov` int(11) NOT NULL,
  `fecha_mov` date NOT NULL,
  `codacceso` int(11) NOT NULL,
  `codigooficina` int(11) NOT NULL,
  `codigopersonal` int(11) NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_producto`
--

CREATE TABLE IF NOT EXISTS `historial_producto` (
`codigohistprod` int(11) NOT NULL,
  `codigoprod` int(11) unsigned zerofill NOT NULL,
  `precio_compra` decimal(7,2) NOT NULL,
  `precio_venta` decimal(7,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `codigoproveedor` int(11) NOT NULL,
  `detalle_producto` varchar(1000) NOT NULL,
  `fecha` varchar(25) NOT NULL,
  `comprobante` varchar(20) NOT NULL,
  `numero` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hist_serv_atendidos`
--

CREATE TABLE IF NOT EXISTS `hist_serv_atendidos` (
`codigoservatendidos` int(11) NOT NULL,
  `codigosv` int(11) NOT NULL,
  `codigosao` int(11) NOT NULL,
  `usuario_atendidos` int(11) NOT NULL,
  `personal_atendidos` int(11) NOT NULL,
  `fecha_atendidos` date NOT NULL,
  `hora_atendidos` time NOT NULL,
  `observacion_atendidos` text COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hist_serv_enproceso`
--

CREATE TABLE IF NOT EXISTS `hist_serv_enproceso` (
`codigoservenproceso` int(11) NOT NULL,
  `codigosv` int(11) NOT NULL,
  `codigosao` int(11) NOT NULL,
  `usuario_enproceso` int(11) NOT NULL,
  `personal_enproceso` int(11) NOT NULL,
  `fecha_enproceso` date NOT NULL,
  `hora_enproceso` time NOT NULL,
  `observacion_enproceso` text COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `igv`
--

CREATE TABLE IF NOT EXISTS `igv` (
`codigoigv` int(11) NOT NULL,
  `igv` int(3) NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_bienes`
--

CREATE TABLE IF NOT EXISTS `inventario_bienes` (
`codigoinventario` int(11) unsigned zerofill NOT NULL,
  `codigo` char(20) COLLATE utf8_spanish2_ci NOT NULL,
  `nombre_bien` varchar(200) COLLATE utf8_spanish2_ci NOT NULL,
  `serie` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `descripcion_bien` text COLLATE utf8_spanish2_ci NOT NULL,
  `fecha_adquisicion` date NOT NULL,
  `numero_factura` char(20) COLLATE utf8_spanish2_ci NOT NULL,
  `fecha_incorporacion` date NOT NULL,
  `precio_compra` decimal(7,2) NOT NULL,
  `codigomarca` int(11) NOT NULL,
  `codigocat` int(11) NOT NULL,
  `codigosubcat` int(11) NOT NULL,
  `codigocolor` int(11) NOT NULL,
  `codigopresent` int(11) NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE IF NOT EXISTS `marca` (
`codigomarca` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `img` varchar(200) DEFAULT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motivorecepcionpc`
--

CREATE TABLE IF NOT EXISTS `motivorecepcionpc` (
`codigomotpc` int(11) NOT NULL,
  `codigoequipo` int(11) NOT NULL,
  `codigopersonal` int(11) unsigned zerofill NOT NULL,
  `motivo` varchar(1000) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_sueldo`
--

CREATE TABLE IF NOT EXISTS `movimiento_sueldo` (
`codigomov` int(11) NOT NULL,
  `codigosueldo` int(11) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `monto` float NOT NULL,
  `motivo` varchar(500) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oficina`
--

CREATE TABLE IF NOT EXISTS `oficina` (
`codigooficina` int(11) NOT NULL,
  `nombre_oficina` varchar(200) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE IF NOT EXISTS `personal` (
`codigopersonal` int(11) NOT NULL,
  `cedula` char(11) COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `paterno` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `materno` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `fecha_nac` date NOT NULL,
  `direccion` varchar(80) COLLATE utf8_spanish2_ci NOT NULL,
  `celular` varchar(14) COLLATE utf8_spanish2_ci NOT NULL,
  `codigoprofesion` int(11) NOT NULL,
  `fecha_ingreso` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` int(1) NOT NULL DEFAULT '0',
  `asignacion_acceso` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`codigopersonal`, `cedula`, `nombre`, `paterno`, `materno`, `fecha_nac`, `direccion`, `celular`, `codigoprofesion`, `fecha_ingreso`, `estado`, `asignacion_acceso`) VALUES
(1, '12345678912', 'RAUL ALBERTO', 'HERNANDEZ', 'MANRIQUE', '1987-06-05', 'MACHALA', '972 - 856 6593', 2, '2016-05-04 15:47:04', 0, 1),
(2, '22222222222', 'ROBERTO', 'VELASQUEZ', 'HERRERA', '2016-05-11', 'PUYANGO TUMBES', '222 - 222 2222', 2, '2016-05-06 02:12:47', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planilla`
--

CREATE TABLE IF NOT EXISTS `planilla` (
  `codigoplanilla` int(11) NOT NULL,
  `codigopersonal` int(11) NOT NULL,
  `codigosueldo` int(11) NOT NULL,
  `codigomov` int(11) NOT NULL,
  `fecha_pago` int(11) NOT NULL,
  `bonos` int(11) NOT NULL,
  `totaladelantos` int(11) NOT NULL,
  `totaldescuentos` int(11) NOT NULL,
  `netopagar` int(11) NOT NULL,
  `dias_laborados` int(11) NOT NULL,
  `mes_pago` int(11) NOT NULL,
  `año_pago` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacion`
--

CREATE TABLE IF NOT EXISTS `presentacion` (
`codigopresent` int(11) NOT NULL,
  `nombre_presentacion` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE IF NOT EXISTS `producto` (
`codigoprod` int(11) unsigned zerofill NOT NULL,
  `nombre_producto` varchar(200) NOT NULL,
  `img` varchar(200) DEFAULT NULL,
  `codigomarca` int(11) NOT NULL,
  `codigocat` int(11) NOT NULL,
  `codigosubcat` int(11) NOT NULL,
  `codigocolor` int(11) NOT NULL,
  `codigopresent` int(11) NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_stock`
--

CREATE TABLE IF NOT EXISTS `producto_stock` (
  `codigo` int(11) NOT NULL,
  `codigoprod` int(10) unsigned zerofill NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesion`
--

CREATE TABLE IF NOT EXISTS `profesion` (
`codigoprofesion` int(11) NOT NULL,
  `profesion` varchar(100) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE IF NOT EXISTS `proveedor` (
`codigoproveedor` int(11) NOT NULL,
  `ruc` varchar(13) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `razonsocial` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `direccion` varchar(60) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `fax` varchar(10) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `contacto` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `pais` enum('ar','bo','br','ch','co','ec','pe','ve') NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `telefono` varchar(10) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `celular` varchar(14) NOT NULL,
  `paginaweb` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor_cuentas`
--

CREATE TABLE IF NOT EXISTS `proveedor_cuentas` (
`codprovcue` int(11) NOT NULL,
  `codigoproveedor` int(11) NOT NULL,
  `banco` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `titular` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `numero_cuenta` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `tipo_cuenta` enum('ah','co','cd','ch') COLLATE utf8_spanish2_ci NOT NULL,
  `estado_cuenta` int(1) NOT NULL DEFAULT '0',
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prueba`
--

CREATE TABLE IF NOT EXISTS `prueba` (
`id` int(11) NOT NULL,
  `typeahead_example_2` varchar(200) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE IF NOT EXISTS `servicios` (
`codigosv` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `tipo` int(1) NOT NULL DEFAULT '0' COMMENT '0 = A Pagar - 1 = A Ofrecer',
  `monto` decimal(7,2) DEFAULT NULL,
  `observacion` text NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serviciosaofrecer`
--

CREATE TABLE IF NOT EXISTS `serviciosaofrecer` (
`codigosao` int(11) NOT NULL,
  `codigosv` int(11) NOT NULL,
  `codigoclientej` int(11) NOT NULL DEFAULT '0',
  `codigoclienten` int(11) NOT NULL DEFAULT '0',
  `usuario_recepcion` int(11) NOT NULL,
  `personal_recepcion` int(11) NOT NULL,
  `fecha_recepcion` date NOT NULL,
  `hora_recepcion` time NOT NULL,
  `observacion_recepcion` text COLLATE utf8_spanish2_ci NOT NULL,
  `estado_servicio` enum('R','P','A') COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'R',
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serviciosapagar`
--

CREATE TABLE IF NOT EXISTS `serviciosapagar` (
`codigosap` int(11) NOT NULL,
  `codigosv` int(11) NOT NULL,
  `codacceso` int(11) NOT NULL,
  `codigopersonal` int(11) NOT NULL,
  `nrecibo` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `monto` decimal(7,2) NOT NULL,
  `femision` date NOT NULL,
  `fpago` date NOT NULL,
  `observacion` varchar(500) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategoria`
--

CREATE TABLE IF NOT EXISTS `subcategoria` (
`codigosubcat` int(11) NOT NULL,
  `codigocat` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sueldo_mensual`
--

CREATE TABLE IF NOT EXISTS `sueldo_mensual` (
`codigosueldo` int(11) NOT NULL,
  `codigopersonal` int(11) NOT NULL,
  `sueldomensual` int(4) NOT NULL,
  `fecha_pago` date NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipomoviemiento`
--

CREATE TABLE IF NOT EXISTS `tipomoviemiento` (
`codigotipomov` int(11) NOT NULL,
  `nombre_tipomov` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `tipomoviemiento`
--

INSERT INTO `tipomoviemiento` (`codigotipomov`, `nombre_tipomov`, `estado`) VALUES
(1, 'INVENTARIO INICIAL', 0),
(2, 'BAJA DE BIEN', 0),
(3, 'INTERCAMBIO DE OFICNA', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_pago`
--

CREATE TABLE IF NOT EXISTS `tipo_pago` (
`codigotipopago` int(11) NOT NULL,
  `nobre_tipopago` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE IF NOT EXISTS `ventas` (
`codigoventas` int(11) unsigned zerofill NOT NULL,
  `codigo` char(20) COLLATE utf8_spanish2_ci NOT NULL,
  `codigoventa` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `tipocomprobante` char(3) COLLATE utf8_spanish2_ci NOT NULL,
  `codigobanco` int(11) DEFAULT NULL,
  `numerotarjeta` varchar(50) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `codigocomprobante` int(11) NOT NULL,
  `tipo_pago` char(3) COLLATE utf8_spanish2_ci NOT NULL,
  `codigoclienten` int(11) DEFAULT NULL,
  `codigoclientej` int(11) DEFAULT NULL,
  `subtotal` double NOT NULL,
  `igv` double NOT NULL,
  `total` double NOT NULL,
  `fecha_emision` date NOT NULL,
  `hora_emision` time NOT NULL,
  `codacceso` int(11) NOT NULL,
  `codigopersonal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vt_detalle_ventas`
--
CREATE TABLE IF NOT EXISTS `vt_detalle_ventas` (
`codigodetalleproducto` int(11) unsigned zerofill
,`codigo` char(20)
,`codigoprod` int(11) unsigned zerofill
,`cantidad` int(6)
,`Producto` varchar(200)
,`Marca` varchar(100)
,`precio_venta` decimal(7,2)
,`nombre_color` varchar(50)
,`Importe` decimal(39,2)
,`stock` int(11)
,`precio_compra` decimal(7,2)
,`pventa` float
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vt_producto`
--
CREATE TABLE IF NOT EXISTS `vt_producto` (
`codigoprod` int(11) unsigned zerofill
,`nombre_producto` varchar(200)
,`codigomarca` int(11)
,`Marca` varchar(100)
,`nombre_color` varchar(50)
,`precio_venta` decimal(7,2)
,`codigohistprod` int(11)
,`stock` int(11)
);
-- --------------------------------------------------------

--
-- Estructura para la vista `vt_detalle_ventas`
--
DROP TABLE IF EXISTS `vt_detalle_ventas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vt_detalle_ventas` AS select `a`.`codigodetalleproducto` AS `codigodetalleproducto`,`a`.`codigo` AS `codigo`,`a`.`codigoprod` AS `codigoprod`,`a`.`cantidad` AS `cantidad`,`b`.`nombre_producto` AS `Producto`,`c`.`nombre` AS `Marca`,`d`.`precio_venta` AS `precio_venta`,`e`.`nombre_color` AS `nombre_color`,sum((`a`.`cantidad` * `d`.`precio_venta`)) AS `Importe`,`ps`.`stock` AS `stock`,`d`.`precio_compra` AS `precio_compra`,`a`.`pventa` AS `pventa` from (((((`detalle_ventas` `a` join `producto` `b` on((`a`.`codigoprod` = `b`.`codigoprod`))) join `marca` `c` on((`b`.`codigomarca` = `c`.`codigomarca`))) join `historial_producto` `d` on((`a`.`codigoprod` = `d`.`codigoprod`))) join `color` `e` on((`b`.`codigocolor` = `e`.`codigocolor`))) join `producto_stock` `ps` on((`ps`.`codigoprod` = `a`.`codigoprod`))) where (`d`.`precio_compra` > 0) group by `a`.`codigoprod` desc;

-- --------------------------------------------------------

--
-- Estructura para la vista `vt_producto`
--
DROP TABLE IF EXISTS `vt_producto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vt_producto` AS select `a`.`codigoprod` AS `codigoprod`,`a`.`nombre_producto` AS `nombre_producto`,`b`.`codigomarca` AS `codigomarca`,`b`.`nombre` AS `Marca`,`c`.`nombre_color` AS `nombre_color`,`d`.`precio_venta` AS `precio_venta`,`d`.`codigohistprod` AS `codigohistprod`,`e`.`stock` AS `stock` from (((((`producto` `a` join `marca` `b` on((`a`.`codigomarca` = `b`.`codigomarca`))) join `color` `c` on((`a`.`codigocolor` = `c`.`codigocolor`))) join `historial_producto` `d` on((`a`.`codigoprod` = `d`.`codigoprod`))) join `producto_stock` `e` on((`a`.`codigoprod` = `e`.`codigoprod`))) left join `detalle_ventas` `dt` on((`dt`.`codigoprod` <> `a`.`codigoprod`))) where ((`a`.`estado` = 0) and (`d`.`precio_compra` > 0)) group by `a`.`codigoprod` order by `d`.`codigohistprod` desc;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acceso`
--
ALTER TABLE `acceso`
 ADD PRIMARY KEY (`codacceso`), ADD UNIQUE KEY `usuario` (`usuario`), ADD KEY `codigopersonal` (`codigopersonal`);

--
-- Indices de la tabla `acceso_historial`
--
ALTER TABLE `acceso_historial`
 ADD PRIMARY KEY (`codhistacceso`);

--
-- Indices de la tabla `banco`
--
ALTER TABLE `banco`
 ADD PRIMARY KEY (`codigobanco`);

--
-- Indices de la tabla `caracteristica_equipo`
--
ALTER TABLE `caracteristica_equipo`
 ADD PRIMARY KEY (`codcaracprod`), ADD KEY `codigoprod` (`codigoprod`);

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
 ADD PRIMARY KEY (`codcar`);

--
-- Indices de la tabla `cargo_personal`
--
ALTER TABLE `cargo_personal`
 ADD PRIMARY KEY (`codcarper`), ADD KEY `codigopersonal` (`codigopersonal`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
 ADD PRIMARY KEY (`codigocat`), ADD UNIQUE KEY `nombre` (`nombre`), ADD UNIQUE KEY `nombre_2` (`nombre`);

--
-- Indices de la tabla `cjuridico`
--
ALTER TABLE `cjuridico`
 ADD PRIMARY KEY (`codigoclientej`);

--
-- Indices de la tabla `cnatural`
--
ALTER TABLE `cnatural`
 ADD PRIMARY KEY (`codigoclienten`);

--
-- Indices de la tabla `color`
--
ALTER TABLE `color`
 ADD PRIMARY KEY (`codigocolor`), ADD UNIQUE KEY `nombre_color` (`nombre_color`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
 ADD PRIMARY KEY (`codconf`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
 ADD PRIMARY KEY (`codigodetalleproducto`), ADD UNIQUE KEY `concatenacion` (`concatenacion`);

--
-- Indices de la tabla `equipo_computo`
--
ALTER TABLE `equipo_computo`
 ADD PRIMARY KEY (`codigoequipo`), ADD KEY `codigoclientej` (`codigoclientej`), ADD KEY `codigoclienten` (`codigoclienten`);

--
-- Indices de la tabla `historial_mov_invbien`
--
ALTER TABLE `historial_mov_invbien`
 ADD PRIMARY KEY (`codigo_hmib`);

--
-- Indices de la tabla `historial_producto`
--
ALTER TABLE `historial_producto`
 ADD PRIMARY KEY (`codigohistprod`);

--
-- Indices de la tabla `hist_serv_atendidos`
--
ALTER TABLE `hist_serv_atendidos`
 ADD PRIMARY KEY (`codigoservatendidos`);

--
-- Indices de la tabla `hist_serv_enproceso`
--
ALTER TABLE `hist_serv_enproceso`
 ADD PRIMARY KEY (`codigoservenproceso`);

--
-- Indices de la tabla `igv`
--
ALTER TABLE `igv`
 ADD PRIMARY KEY (`codigoigv`);

--
-- Indices de la tabla `inventario_bienes`
--
ALTER TABLE `inventario_bienes`
 ADD PRIMARY KEY (`codigoinventario`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
 ADD PRIMARY KEY (`codigomarca`), ADD UNIQUE KEY `nombre` (`nombre`), ADD UNIQUE KEY `nombre_2` (`nombre`);

--
-- Indices de la tabla `motivorecepcionpc`
--
ALTER TABLE `motivorecepcionpc`
 ADD PRIMARY KEY (`codigomotpc`), ADD KEY `codigoequipo` (`codigoequipo`);

--
-- Indices de la tabla `movimiento_sueldo`
--
ALTER TABLE `movimiento_sueldo`
 ADD PRIMARY KEY (`codigomov`), ADD KEY `codigosueldo` (`codigosueldo`);

--
-- Indices de la tabla `oficina`
--
ALTER TABLE `oficina`
 ADD PRIMARY KEY (`codigooficina`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
 ADD PRIMARY KEY (`codigopersonal`);

--
-- Indices de la tabla `presentacion`
--
ALTER TABLE `presentacion`
 ADD PRIMARY KEY (`codigopresent`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
 ADD PRIMARY KEY (`codigoprod`), ADD KEY `codigomarca` (`codigomarca`);

--
-- Indices de la tabla `profesion`
--
ALTER TABLE `profesion`
 ADD PRIMARY KEY (`codigoprofesion`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
 ADD PRIMARY KEY (`codigoproveedor`);

--
-- Indices de la tabla `proveedor_cuentas`
--
ALTER TABLE `proveedor_cuentas`
 ADD PRIMARY KEY (`codprovcue`);

--
-- Indices de la tabla `prueba`
--
ALTER TABLE `prueba`
 ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
 ADD PRIMARY KEY (`codigosv`), ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `serviciosaofrecer`
--
ALTER TABLE `serviciosaofrecer`
 ADD PRIMARY KEY (`codigosao`);

--
-- Indices de la tabla `serviciosapagar`
--
ALTER TABLE `serviciosapagar`
 ADD PRIMARY KEY (`codigosap`), ADD UNIQUE KEY `nrecibo` (`nrecibo`);

--
-- Indices de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
 ADD PRIMARY KEY (`codigosubcat`), ADD KEY `codigocat` (`codigocat`);

--
-- Indices de la tabla `sueldo_mensual`
--
ALTER TABLE `sueldo_mensual`
 ADD PRIMARY KEY (`codigosueldo`), ADD KEY `codigopersonal` (`codigopersonal`);

--
-- Indices de la tabla `tipomoviemiento`
--
ALTER TABLE `tipomoviemiento`
 ADD PRIMARY KEY (`codigotipomov`);

--
-- Indices de la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
 ADD PRIMARY KEY (`codigotipopago`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
 ADD PRIMARY KEY (`codigoventas`), ADD UNIQUE KEY `codigoventa` (`codigoventa`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acceso`
--
ALTER TABLE `acceso`
MODIFY `codacceso` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `acceso_historial`
--
ALTER TABLE `acceso_historial`
MODIFY `codhistacceso` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT de la tabla `banco`
--
ALTER TABLE `banco`
MODIFY `codigobanco` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `caracteristica_equipo`
--
ALTER TABLE `caracteristica_equipo`
MODIFY `codcaracprod` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
MODIFY `codcar` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `cargo_personal`
--
ALTER TABLE `cargo_personal`
MODIFY `codcarper` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
MODIFY `codigocat` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `cjuridico`
--
ALTER TABLE `cjuridico`
MODIFY `codigoclientej` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `cnatural`
--
ALTER TABLE `cnatural`
MODIFY `codigoclienten` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `color`
--
ALTER TABLE `color`
MODIFY `codigocolor` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
MODIFY `codconf` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
MODIFY `codigodetalleproducto` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `equipo_computo`
--
ALTER TABLE `equipo_computo`
MODIFY `codigoequipo` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `historial_mov_invbien`
--
ALTER TABLE `historial_mov_invbien`
MODIFY `codigo_hmib` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `historial_producto`
--
ALTER TABLE `historial_producto`
MODIFY `codigohistprod` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `hist_serv_atendidos`
--
ALTER TABLE `hist_serv_atendidos`
MODIFY `codigoservatendidos` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `hist_serv_enproceso`
--
ALTER TABLE `hist_serv_enproceso`
MODIFY `codigoservenproceso` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `igv`
--
ALTER TABLE `igv`
MODIFY `codigoigv` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `inventario_bienes`
--
ALTER TABLE `inventario_bienes`
MODIFY `codigoinventario` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
MODIFY `codigomarca` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `motivorecepcionpc`
--
ALTER TABLE `motivorecepcionpc`
MODIFY `codigomotpc` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `movimiento_sueldo`
--
ALTER TABLE `movimiento_sueldo`
MODIFY `codigomov` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `oficina`
--
ALTER TABLE `oficina`
MODIFY `codigooficina` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
MODIFY `codigopersonal` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `presentacion`
--
ALTER TABLE `presentacion`
MODIFY `codigopresent` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
MODIFY `codigoprod` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `profesion`
--
ALTER TABLE `profesion`
MODIFY `codigoprofesion` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
MODIFY `codigoproveedor` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `proveedor_cuentas`
--
ALTER TABLE `proveedor_cuentas`
MODIFY `codprovcue` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `prueba`
--
ALTER TABLE `prueba`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
MODIFY `codigosv` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `serviciosaofrecer`
--
ALTER TABLE `serviciosaofrecer`
MODIFY `codigosao` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `serviciosapagar`
--
ALTER TABLE `serviciosapagar`
MODIFY `codigosap` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
MODIFY `codigosubcat` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sueldo_mensual`
--
ALTER TABLE `sueldo_mensual`
MODIFY `codigosueldo` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tipomoviemiento`
--
ALTER TABLE `tipomoviemiento`
MODIFY `codigotipomov` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
MODIFY `codigotipopago` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
MODIFY `codigoventas` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acceso`
--
ALTER TABLE `acceso`
ADD CONSTRAINT `acceso_ibfk_1` FOREIGN KEY (`codigopersonal`) REFERENCES `personal` (`codigopersonal`);

--
-- Filtros para la tabla `cargo_personal`
--
ALTER TABLE `cargo_personal`
ADD CONSTRAINT `cargo_personal_ibfk_1` FOREIGN KEY (`codigopersonal`) REFERENCES `personal` (`codigopersonal`);

--
-- Filtros para la tabla `equipo_computo`
--
ALTER TABLE `equipo_computo`
ADD CONSTRAINT `equipo_computo_ibfk_3` FOREIGN KEY (`codigoclienten`) REFERENCES `cnatural` (`codigoclienten`),
ADD CONSTRAINT `equipo_computo_ibfk_4` FOREIGN KEY (`codigoclientej`) REFERENCES `cjuridico` (`codigoclientej`);

--
-- Filtros para la tabla `motivorecepcionpc`
--
ALTER TABLE `motivorecepcionpc`
ADD CONSTRAINT `motivorecepcionpc_ibfk_1` FOREIGN KEY (`codigoequipo`) REFERENCES `equipo_computo` (`codigoequipo`);

--
-- Filtros para la tabla `movimiento_sueldo`
--
ALTER TABLE `movimiento_sueldo`
ADD CONSTRAINT `movimiento_sueldo_ibfk_1` FOREIGN KEY (`codigosueldo`) REFERENCES `sueldo_mensual` (`codigosueldo`);

--
-- Filtros para la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
ADD CONSTRAINT `subcategoria_ibfk_1` FOREIGN KEY (`codigocat`) REFERENCES `categoria` (`codigocat`);

--
-- Filtros para la tabla `sueldo_mensual`
--
ALTER TABLE `sueldo_mensual`
ADD CONSTRAINT `sueldo_mensual_ibfk_1` FOREIGN KEY (`codigopersonal`) REFERENCES `personal` (`codigopersonal`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
