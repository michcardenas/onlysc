<!DOCTYPE html>
<html>
<head>
   <meta name="robots" content="noindex,nofollow">
   <title>403 - Acceso Prohibido</title>
   <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
   <style>
       .bg-custom-red {
           background-color: #e00037;
       }
       .hover\:bg-custom-red-dark:hover {
           background-color: #c80033;
       }
   </style>
</head>

<body>
   <div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center px-4">
       <div class="text-center">
           <img src="{{ asset('images/logo_v2.png') }}" alt="Logo" class="mx-auto mb-8 w-64">
           
           <h1 class="text-9xl font-bold text-gray-800">403</h1>
           <h2 class="text-2xl md:text-4xl font-bold text-gray-700 mt-4">
               ¡Acceso Prohibido!
           </h2>
           <p class="text-gray-500 mt-6 mb-8">
               Lo sentimos, no tienes permisos para acceder a esta página.
           </p>
           
           <div class="flex flex-col sm:flex-row gap-4 justify-center">
               <a href="{{ route('home') }}" 
                  class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-custom-red text-white rounded-lg hover:bg-custom-red-dark transition-colors">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                   </svg>
                   Volver al inicio
               </a>
               
               <button onclick="window.history.back()" 
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                   </svg>
                   Regresar
               </button>
           </div>
       </div>
   </div>
</body>
</html>