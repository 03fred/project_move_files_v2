<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Controllers\Action;

use Psr\Http\Message\ResponseInterface;
use SimpleXLSX;

class FileController extends action
{

    public function process($request, ResponseInterface $response,  $args)
    {
        $data      = (object) $request->getParsedBody();
        $path      = $data->directory;
        $newPath   = $data->newDirectory;
        $files     = [];
        $filesPath = [];

        if (!is_dir($path)) {
            $response->getBody()->write(json_encode(['msg' => 'Diretorio não encontrado!']));
            return $response
                ->withStatus(400);
        }

        if (!is_dir($newPath)) {
            $response->getBody()->write(json_encode(['msg' => 'Novo diretorio não encontrado!']));
            return $response
                ->withStatus(400);
        }

        $uploadedFiles = $request->getUploadedFiles();

        $uploadedFile = $uploadedFiles['fileUpload'];

        $extensao = explode(".", $uploadedFile->getClientFilename())[1];

        if ($extensao !== 'xlsx') {
            $response->getBody()->write(json_encode(['msg' => 'Extensão não suportada! Aceita se apenas arquivos de extensão .xlsx']));
            return $response
                ->withStatus(400);
        }

        $directory = '../temp/file.xlsx';

        //Salva o arquivo em um diretorio temporario
        $uploadedFile->moveTo($directory);

        $xlsx  = SimpleXLSX::parse($directory);

        $directory = dir($path);

        foreach ($xlsx->rows() as $file) {
            array_push($files, $file[0]);
        }

        $diretorio = dir($path);
        while ($f = $directory->read()) {
            if (in_array($f, $files)) {
                copy($path . '/' . $f, $newPath . '/' . $f);
            }

            array_push($filesPath, $f);
        }

        $diretorio->close();

        $filesNotFound =  array_diff($files, $filesPath);

        $str = count($filesNotFound) == 0 ? "Todos os arquivos foram inseridos" : "Seguinte arquivos não foram inseridos " . (implode(",", $filesNotFound)) . ".";

        //retorna a resposta em json
        $response->getBody()->write(json_encode(["msg" => $str]));
        return $response
            ->withStatus(201);
    }



    /**
     * {@inheritdoc}
     */
    protected function action()
    {
    }
}
