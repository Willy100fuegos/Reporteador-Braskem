<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reporte de Novedades | SESCA - Braskem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', system-ui, sans-serif; }
        .input-field { @apply w-full bg-white text-gray-700 border border-gray-300 rounded-lg py-3 px-4 mb-3 focus:outline-none focus:bg-white focus:border-blue-500 transition duration-200; }
        .btn-primary { @apply w-full bg-blue-900 text-white font-bold py-4 px-4 rounded-lg shadow hover:bg-blue-800 transition duration-200 flex justify-center items-center gap-2; }
        .upload-box { @apply h-24 border-2 border-dashed border-gray-300 rounded-lg flex flex-col justify-center items-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden; }
        .loader { border-top-color: #3498db; -webkit-animation: spinner 1.5s linear infinite; animation: spinner 1.5s linear infinite; }
        @keyframes spinner { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        /* Aviso de borrador */
        .draft-notice { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body class="pb-10">

    <!-- Header -->
    <div class="bg-blue-900 text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="flex justify-between items-center max-w-2xl mx-auto">
            <img src="http://imgfz.com/i/ioW1kPu.png" alt="SESCA" class="h-10 bg-white rounded p-1">
            <h1 class="text-sm md:text-lg font-bold tracking-wider text-center leading-tight">
                REPORTE DE NOVEDADES<br>
                <span class="text-xs font-normal opacity-80">SESCA SEGURIDAD PRIVADA</span>
            </h1>
            <img src="http://imgfz.com/i/z5weTEV.png" alt="Braskem" class="h-10 bg-white rounded p-1">
        </div>
    </div>

    <!-- Draft Notice Container -->
    <div id="draftMessage" class="max-w-2xl mx-auto px-4 mt-4 hidden">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 shadow-sm rounded-r draft-notice flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-history text-yellow-600 mr-3 text-xl"></i>
                <div>
                    <p class="text-sm text-yellow-700 font-bold">Borrador recuperado</p>
                    <p class="text-xs text-yellow-600">Hemos restaurado tu reporte pendiente.</p>
                </div>
            </div>
            <button onclick="clearDraft()" class="text-xs text-gray-500 underline hover:text-red-500">Descartar</button>
        </div>
    </div>

    <!-- Form Container -->
    <div class="max-w-2xl mx-auto px-4 mt-6">
        <form id="reportForm" action="procesar.php" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-md p-6">
            
            <h2 class="text-gray-800 text-xl font-bold mb-6 border-b pb-2 text-blue-900">
                <i class="fas fa-file-signature mr-2"></i>Nueva Novedad
            </h2>

            <!-- 1. Supervisor -->
            <label class="block text-gray-700 text-sm font-bold mb-1">Supervisor</label>
            <div class="relative mb-3">
                <select name="supervisor" id="supervisor" required class="input-field appearance-none save-target">
                    <option value="" disabled selected>Seleccione su nombre...</option>
                    <option value="José Guadalupe Córdoba Ventura">José Guadalupe Córdoba Ventura</option>
                    <option value="Olegario Reyes Arnabar">Olegario Reyes Arnabar</option>
                    <option value="Uriel Francisco Garfias Vela">Uriel Francisco Garfias Vela</option>
                    <option value="Jesús Antonio Pérez Pérez">Jesús Antonio Pérez Pérez</option>
                    <option value="Raúl de Lucio Rincón">Raúl de Lucio Rincón</option>
                    <option value="Teresa de Jesús Chalé Salinas">Teresa de Jesús Chalé Salinas</option>
                    <option value="César Noe Ruiz Soto">César Noe Ruiz Soto</option>
                    <option value="Martín Pérez Bautista">Martín Pérez Bautista</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700 pb-3">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- 2. Fecha y Hora -->
            <label class="block text-gray-700 text-sm font-bold mb-1">Fecha y Hora del Suceso</label>
            <input type="datetime-local" name="fecha_suceso" id="fecha_suceso" required class="input-field save-target">

            <!-- 3. Área -->
            <label class="block text-gray-700 text-sm font-bold mb-1">Área</label>
            <div class="relative mb-3">
                <select name="area" id="area" required class="input-field appearance-none save-target">
                    <option value="Braskem IDESA">Braskem IDESA</option>
                    <option value="Predio Benjamín">Predio Benjamín</option>
                    <option value="Otro">Otro</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700 pb-3">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- 4. Ubicación -->
            <label class="block text-gray-700 text-sm font-bold mb-1">Ubicación Exacta</label>
            <div class="relative mb-3">
                <select id="selectUbicacion" class="input-field appearance-none save-target" onchange="toggleUbicacion(this.value)">
                    <option value="" disabled selected>Seleccione ubicación...</option>
                    <option value="Bravo 1">Bravo 1</option>
                    <option value="Bravo 2">Bravo 2</option>
                    <option value="Bravo 3">Bravo 3</option>
                    <option value="Bravo 4">Bravo 4</option>
                    <option value="Alfa 1">Alfa 1</option>
                    <option value="Alfa 2">Alfa 2</option>
                    <option value="Alfa 3">Alfa 3</option>
                    <option value="Alfa 4">Alfa 4</option>
                    <option value="Alfa 5">Alfa 5</option>
                    <option value="Alfa 6">Alfa 6</option>
                    <option value="Caseta Norte">Caseta Norte</option>
                    <option value="Otro">Otro (Escribir manual)</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700 pb-3">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
            <input type="text" name="ubicacion" id="inputUbicacion" placeholder="Especifique la ubicación..." class="input-field hidden save-target" required>

            <!-- 5. Título del Suceso -->
            <label class="block text-gray-700 text-sm font-bold mb-1">¿Qué sucedió? (Título Breve)</label>
            <input type="text" name="titulo_suceso" id="titulo_suceso" placeholder="Ej. Robo de Cable, Intrusión, Daño a propiedad..." required class="input-field save-target">

            <!-- 6. Involucrados -->
            <label class="block text-gray-700 text-sm font-bold mb-1">¿Quiénes? (Involucrados)</label>
            <input type="text" name="involucrados" id="involucrados" placeholder="Personas, Empresas o 'Por identificar'..." required class="input-field save-target">

            <!-- 7. Descripción -->
            <label class="block text-gray-700 text-sm font-bold mb-1 flex justify-between items-center mt-4">
                Descripción Detallada
                <button type="button" id="btnIA" class="text-xs bg-purple-100 text-purple-700 px-3 py-1 rounded-full border border-purple-200 hover:bg-purple-200 transition font-bold shadow-sm">
                    <i class="fas fa-magic mr-1"></i> Mejorar Redacción
                </button>
            </label>
            <div class="relative">
                <textarea id="descripcion" name="descripcion" rows="5" required class="input-field mb-1 save-target" placeholder="Narre los hechos cronológicamente..."></textarea>
                <input type="hidden" name="descripcion_ia" id="input_ia_used" value="0">
                
                <div id="loaderIA" class="hidden absolute inset-0 bg-white/90 flex flex-col justify-center items-center backdrop-blur-sm rounded-lg z-10">
                    <div class="loader ease-linear rounded-full border-4 border-t-4 border-purple-500 h-10 w-10 mb-3"></div>
                    <span class="text-xs font-bold text-purple-800 animate-pulse">Optimizando texto...</span>
                </div>
            </div>

            <!-- 8. Fotos -->
            <label class="block text-gray-700 text-sm font-bold mb-2 mt-4">Evidencia Fotográfica (Max 3)</label>
            <div class="flex gap-2 mb-6">
                <div class="upload-box flex-1" onclick="document.getElementById('img1').click()">
                    <input type="file" name="img1" id="img1" accept="image/*" class="hidden" onchange="previewImage(this, 'prev1')">
                    <div id="prev1" class="text-gray-400 text-center">
                        <i class="fas fa-camera text-xl mb-1"></i><br><span class="text-[10px]">Foto 1</span>
                    </div>
                </div>
                <div class="upload-box flex-1" onclick="document.getElementById('img2').click()">
                    <input type="file" name="img2" id="img2" accept="image/*" class="hidden" onchange="previewImage(this, 'prev2')">
                    <div id="prev2" class="text-gray-400 text-center">
                        <i class="fas fa-camera text-xl mb-1"></i><br><span class="text-[10px]">Foto 2</span>
                    </div>
                </div>
                <div class="upload-box flex-1" onclick="document.getElementById('img3').click()">
                    <input type="file" name="img3" id="img3" accept="image/*" class="hidden" onchange="previewImage(this, 'prev3')">
                    <div id="prev3" class="text-gray-400 text-center">
                        <i class="fas fa-camera text-xl mb-1"></i><br><span class="text-[10px]">Foto 3</span>
                    </div>
                </div>
            </div>

            <button type="submit" id="btnSubmit" class="btn-primary group">
                <span class="group-hover:scale-105 transition-transform">ENVIAR REPORTE</span>
                <i class="fas fa-paper-plane"></i>
            </button>

        </form>
    </div>

    <!-- Overlay Submit -->
    <div id="overlaySubmit" class="hidden fixed inset-0 bg-blue-900/90 z-[60] flex flex-col justify-center items-center text-white backdrop-blur-sm">
        <div class="loader ease-linear rounded-full border-4 border-t-4 border-white h-12 w-12 mb-4"></div>
        <h2 class="text-2xl font-bold">Procesando</h2>
        <p class="text-sm opacity-80 mt-2">Generando PDF y enviando correos...</p>
    </div>

    <script>
        // --- SISTEMA DE AUTOGUARDADO (CACHE LOCAL) ---
        const CACHE_KEY = 'reporte_draft_c4';
        
        // 1. Guardar cambios en tiempo real
        function initAutoSave() {
            const inputs = document.querySelectorAll('.save-target');
            inputs.forEach(input => {
                input.addEventListener('input', saveDraft);
                input.addEventListener('change', saveDraft);
            });
        }

        function saveDraft() {
            const data = {};
            document.querySelectorAll('.save-target').forEach(input => {
                data[input.id] = input.value;
            });
            localStorage.setItem(CACHE_KEY, JSON.stringify(data));
        }

        // 2. Restaurar al cargar
        function loadDraft() {
            const saved = localStorage.getItem(CACHE_KEY);
            if (saved) {
                const data = JSON.parse(saved);
                let hasData = false;
                
                // Rellenar campos
                for (const key in data) {
                    const el = document.getElementById(key);
                    if (el && data[key]) {
                        el.value = data[key];
                        hasData = true;
                        
                        // Trigger manual para inputs condicionales
                        if (key === 'selectUbicacion') toggleUbicacion(data[key]);
                    }
                }
                
                if (hasData) {
                    document.getElementById('draftMessage').classList.remove('hidden');
                    Swal.fire({
                        icon: 'info',
                        title: 'Borrador Recuperado',
                        text: 'Hemos restaurado tu información pendiente. Por favor, verifica las fotos.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            }
        }

        // 3. Limpiar cache (al enviar o cancelar)
        function clearDraft() {
            localStorage.removeItem(CACHE_KEY);
            location.reload(); // Recargar para limpiar campos
        }

        // --- FUNCIONES ORIGINALES ---

        function toggleUbicacion(val) {
            const input = document.getElementById('inputUbicacion');
            if(val === 'Otro') {
                input.classList.remove('hidden');
                input.placeholder = "Escriba la ubicación exacta...";
            } else {
                input.classList.add('hidden');
                input.value = val; 
            }
            saveDraft(); // Guardar cambio de estado
        }
        
        document.addEventListener('DOMContentLoaded', () => {
             loadDraft(); // Intentar recuperar borrador
             initAutoSave(); // Iniciar escuchas
        });

        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.getElementById(previewId);
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    div.parentElement.classList.add('border-green-500', 'bg-green-50');
                    div.parentElement.classList.remove('border-gray-300');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Lógica IA
        document.getElementById('btnIA').addEventListener('click', async () => {
            const txtArea = document.getElementById('descripcion');
            const texto = txtArea.value.trim();
            const loader = document.getElementById('loaderIA');

            if(texto.length < 5) {
                Swal.fire('Atención', 'Escribe primero una idea básica.', 'warning');
                return;
            }

            loader.classList.remove('hidden');

            try {
                const response = await fetch('ia_proxy.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({texto: texto})
                });
                
                const textResponse = await response.text();
                let data;
                try {
                    data = JSON.parse(textResponse);
                } catch(e) { throw new Error("Error servidor: " + textResponse.substring(0,50)); }

                if(data.texto_mejorado) {
                    txtArea.value = data.texto_mejorado;
                    document.getElementById('input_ia_used').value = "1";
                    saveDraft(); // Guardar el texto mejorado
                    Swal.fire({
                        icon: 'success',
                        title: 'Redacción Mejorada',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true, position: 'top-end'
                    });
                } else {
                    throw new Error(data.error || 'Error desconocido');
                }
            } catch (error) {
                Swal.fire('Error IA', error.message, 'error');
            } finally {
                loader.classList.add('hidden');
            }
        });

        // Submit Handler
        document.getElementById('reportForm').addEventListener('submit', function() {
            document.getElementById('overlaySubmit').classList.remove('hidden');
            // Borramos el borrador justo antes de enviar para evitar conflictos futuros
            localStorage.removeItem(CACHE_KEY);
        });
    </script>
</body>
</html>