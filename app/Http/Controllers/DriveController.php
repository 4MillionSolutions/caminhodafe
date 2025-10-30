<?php
namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

class DriveController extends Controller
{
    protected $drive;

    public function __construct(GoogleDriveService $drive)
    {
        $this->middleware('auth');

        $this->drive = $drive;

    }

    public function listFiles()
    {
        $files = $this->drive->listFiles();


        return view('driveUpload', ['files' => $files]);
    }

    public function upload(Request $request)
    {
        try{
            $request->validate([
                'file' => 'required|file|max:10240',
            ]);

            $pessoa = $request->input('pessoa');
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->getRealPath();
            $name = $pessoa . '_' . $uploadedFile->getClientOriginalName();
            $mimeType = $uploadedFile->getMimeType();

            $file = $this->drive->uploadFile($path, $name, $mimeType);

            return redirect()->route('drive.index')->with('success', 'Arquivo enviado com sucesso!');
        } catch (\Exception $e) {
            info($e->getMessage());
            return redirect()->route('drive.index')->with('error', 'Erro ao enviar arquivo: ' . $e->getMessage());
        }
}

    public function download($id)
    {
        return $this->drive->downloadFile($id);
    }

    public function delete($id)
    {
        $this->drive->deleteFile($id);
        return response()->json(['status' => 'deletado']);
    }
}
