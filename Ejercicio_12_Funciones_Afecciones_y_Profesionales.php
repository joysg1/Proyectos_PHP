<?php
// ==================== FUNCIONES PROFESIONALES ====================
function llamarMedicoGeneral($paciente, $sintoma) {
    return "📞 <strong>Dr. Pérez (Médico General)</strong> atendiendo a <em>$paciente</em><br>
            → Síntoma: $sintoma<br>
            → Acción: Diagnóstico inicial y receta básica<br><br>";
}

function llamarCardiologo($paciente, $sintoma) {
    return "❤️ <strong>Dra. García (Cardióloga)</strong> examinando a <em>$paciente</em><br>
            → Síntoma: $sintoma<br>
            → Acción: Electrocardiograma y evaluación cardiaca<br><br>";
}

function llamarTraumatologo($paciente, $sintoma) {
    return "🦴 <strong>Dr. Rodríguez (Traumatólogo)</strong> tratando a <em>$paciente</em><br>
            → Síntoma: $sintoma<br>
            → Acción: Radiografía y inmovilización<br><br>";
}

function llamarPsicologo($paciente, $sintoma) {
    return "🧠 <strong>Lic. Martínez (Psicólogo)</strong> escuchando a <em>$paciente</em><br>
            → Síntoma: $sintoma<br>
            → Acción: Sesión de terapia y ejercicios mentales<br><br>";
}

function llamarDermatologo($paciente, $sintoma) {
    return "🔬 <strong>Dra. López (Dermatóloga)</strong> revisando a <em>$paciente</em><br>
            → Síntoma: $sintoma<br>
            → Acción: Análisis de piel y tratamiento tópico<br><br>";
}

function llamarNutriologo($paciente, $sintoma) {
    return "🍎 <strong>Lic. Torres (Nutriólogo)</strong> asesorando a <em>$paciente</em><br>
            → Síntoma: $sintoma<br>
            → Acción: Plan alimenticio y recomendaciones nutricionales<br><br>";
}

function llamarFisioterapeuta($paciente, $sintoma) {
    return "💪 <strong>Lic. Sánchez (Fisioterapeuta)</strong> rehabilitando a <em>$paciente</em><br>
            → Síntoma: $sintoma<br>
            → Acción: Ejercicios de rehabilitación y terapia física<br><br>";
}

// ==================== FUNCIÓN DINÁMICA PRINCIPAL ====================
function evaluarPaciente($nombre, $afeccion, $sintomas) {
    $resultado = "<div class='resultado-paciente'>";
    $resultado .= "<h3>🩺 Diagnóstico para: $nombre</h3>";
    $resultado .= "<p><strong>Afección:</strong> $afeccion</p>";
    $resultado .= "<p><strong>Síntomas:</strong> $sintomas</p>";
    $resultado .= "<div class='profesionales-llamados'>";
    
    // Lógica dinámica que decide qué profesionales llamar
    switch(strtolower($afeccion)) {
        case 'problema cardiaco':
            $resultado .= llamarMedicoGeneral($nombre, $sintomas);
            $resultado .= llamarCardiologo($nombre, $sintomas);
            break;
            
        case 'fractura o lesión':
            $resultado .= llamarMedicoGeneral($nombre, $sintomas);
            $resultado .= llamarTraumatologo($nombre, $sintomas);
            $resultado .= llamarFisioterapeuta($nombre, $sintomas);
            break;
            
        case 'ansiedad o estrés':
            $resultado .= llamarPsicologo($nombre, $sintomas);
            $resultado .= llamarMedicoGeneral($nombre, $sintomas);
            break;
            
        case 'problema de piel':
            $resultado .= llamarDermatologo($nombre, $sintomas);
            break;
            
        case 'obesidad o nutrición':
            $resultado .= llamarNutriologo($nombre, $sintomas);
            $resultado .= llamarMedicoGeneral($nombre, $sintomas);
            break;
            
        case 'dolor muscular':
            $resultado .= llamarMedicoGeneral($nombre, $sintomas);
            $resultado .= llamarFisioterapeuta($nombre, $sintomas);
            break;
            
        case 'chequeo general':
            $resultado .= llamarMedicoGeneral($nombre, $sintomas);
            $resultado .= llamarNutriologo($nombre, "Chequeo nutricional");
            break;
            
        default:
            $resultado .= llamarMedicoGeneral($nombre, $sintomas);
            $resultado .= "<p>⚠️ <em>Se recomienda consulta general para diagnóstico preciso</em></p>";
    }
    
    $resultado .= "</div></div>";
    return $resultado;
}

// ==================== PROCESAMIENTO DEL FORMULARIO ====================
$resultadoDiagnostico = "";

if ($_POST) {
    $nombre = htmlspecialchars($_POST['nombre']);
    $afeccion = htmlspecialchars($_POST['afeccion']);
    $sintomas = htmlspecialchars($_POST['sintomas']);
    
    if (!empty($nombre) && !empty($afeccion)) {
        $resultadoDiagnostico = evaluarPaciente($nombre, $afeccion, $sintomas);
    } else {
        $resultadoDiagnostico = "<div class='error'>❌ Por favor, complete todos los campos obligatorios</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Médico con Funciones Dinámicas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        .form-section {
            padding: 40px;
            background: #f8f9fa;
        }
        .result-section {
            padding: 40px;
            background: white;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        .resultado-paciente {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #667eea;
        }
        .profesionales-llamados {
            margin-top: 20px;
        }
        .error {
            background: #ffeaa7;
            color: #d63031;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .profesional-card {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Sistema Médico Dinámico</h1>
            <p>Ejemplo didáctico de funciones dinámicas en PHP</p>
        </div>
        
        <div class="content">
            <div class="form-section">
                <h2>📋 Formulario del Paciente</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nombre">Nombre del Paciente:</label>
                        <input type="text" id="nombre" name="nombre" required 
                               placeholder="Ej: Juan Pérez">
                    </div>
                    
                    <div class="form-group">
                        <label for="afeccion">Tipo de Afección:</label>
                        <select id="afeccion" name="afeccion" required>
                            <option value="">-- Seleccione una afección --</option>
                            <option value="Problema cardiaco">❤️ Problema Cardiaco</option>
                            <option value="Fractura o lesión">🦴 Fractura o Lesión</option>
                            <option value="Ansiedad o estrés">🧠 Ansiedad o Estrés</option>
                            <option value="Problema de piel">🔬 Problema de Piel</option>
                            <option value="Obesidad o nutrición">🍎 Obesidad o Nutrición</option>
                            <option value="Dolor muscular">💪 Dolor Muscular</option>
                            <option value="Chequeo general">🩺 Chequeo General</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sintomas">Descripción de Síntomas:</label>
                        <textarea id="sintomas" name="sintomas" rows="4" 
                                  placeholder="Describa sus síntomas en detalle..."></textarea>
                    </div>
                    
                    <button type="submit">🔍 Realizar Diagnóstico</button>
                </form>
            </div>
            
            <div class="result-section">
                <h2>📊 Resultado del Diagnóstico</h2>
                <?php if ($resultadoDiagnostico): ?>
                    <?php echo $resultadoDiagnostico; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                        <p>👆 Complete el formulario para ver el diagnóstico</p>
                        <p><small>El sistema llamará automáticamente a los profesionales adecuados</small></p>
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 30px; padding: 20px; background: #e8f4fd; border-radius: 8px;">
                    <h3>💡 ¿Cómo funciona?</h3>
                    <p><strong>Función dinámica <code>evaluarPaciente()</code>:</strong></p>
                    <ul style="margin-left: 20px;">
                        <li>Recibe nombre, afección y síntomas</li>
                        <li>Decide qué funciones de profesionales llamar</li>
                        <li>Ejecuta las funciones correspondientes</li>
                        <li>Retorna el resultado combinado</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
