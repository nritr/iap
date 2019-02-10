INSERT INTO `PROSPECTO` (`id_prospecto`, `id_estado`, `id_origen`, `nombre`, `nombre_fb`, `messenger_id`, `celular`, `celular_normalizado`, `edad`, `dni`, `dni_coincide`, `dni_normalizado`, `localidad`, `oficio`, `vivienda`, `servicio_anombrede`, `blanco_negro`, `salario`, `normalizado`, `horario_preferencia`, `fecha`) VALUES (NULL, '27', '3', 'Prueba 1', 'Prueba 1 fb', NULL, '1159384797', '5491159374797', '39', '27592902', 'si', NULL, 'caba', 'sistemas', 'vivienda propia', 'ignacio sclar', 'monotributo', '10000', '0', '15hrs', CURRENT_TIMESTAMP);


INSERT INTO `LOCALIDAD` (`id_localidad`, `id_localidad_access`, `id_region`, `localidad`, `zona_entrega`, `partido`, `horario_entrega`) VALUES 
(NULL, '74', '3', 'Adolfo Sourdeaux', '5', 'Malvinas Argentinas', '11:00 a 15:00'), 
(NULL, '17', '1', 'Bancalari', '1', 'SAN FERNANDO', '9:30 a 14:30'),
(NULL, '5', '1', 'Beccar', '1', 'SAN ISIDRO', '9:30 a 14:30'), 
(NULL, '69', '3', 'Bella Vista', '5', 'San Miguel', '13:30 a 16:00');