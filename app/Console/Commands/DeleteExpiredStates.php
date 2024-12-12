<?php

namespace App\Console\Commands;

use App\Models\Estado;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeleteExpiredStates extends Command
{
    protected $signature = 'estados:delete-expired';
    protected $description = 'Delete estados older than 1 week';

    public function handle()
    {
        $this->info('Iniciando eliminación de estados expirados...');

        try {
            // Cambiar subHours(24) por subWeeks(1) para eliminar estados de más de 1 semana
            $expiredEstados = Estado::where('created_at', '<=', Carbon::now()->subWeeks(1))->get();

            $count = 0;
            foreach ($expiredEstados as $estado) {
                // Si el estado tiene fotos, las eliminamos de storage
                if ($estado->fotos) {
                    Storage::disk('public')->delete($estado->fotos);
                    Log::info('Foto del estado eliminada', [
                        'path' => $estado->fotos
                    ]);
                }
                
                // Eliminamos el estado
                $estado->delete();
                $count++;
                
                // Log de eliminación de estado
                Log::info('Estado expirado eliminado', [
                    'estado_id' => $estado->id,
                    'created_at' => $estado->created_at
                ]);
            }

            $this->info("Se eliminaron {$count} estados expirados");
            Log::info("Proceso de eliminación completado", ['estados_eliminados' => $count]);

        } catch (\Exception $e) {
            $this->error('Error al eliminar estados expirados: ' . $e->getMessage());
            Log::error('Error en comando delete-expired-states', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
        }
    }
}
