<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class BlackList extends Controller
{
    private $inputFilename = 'blacklist_in.txt';
    private $outputFilename = 'blacklist_out.txt';
    private $path = 'C:\\OSPanel\\domains\\arbait\\storage\\app\\';

    public function rewriteFile()
    {
        $status = 'BAD';
        $lineCount = 0;
        $lines = '';

        if (Storage::exists($this->inputFilename)) {
            $status = 'OK';
            foreach (file($this->path . $this->inputFilename) as $line) {
                $lineCount++;
                if (strpos($line, ' - ') != false) {
                    $lines .= substr(strstr($line, ' - '), 3);
                } else {
                    $lines .= $line;
                }
            }
        }

        Storage::append($this->outputFilename, $lines);

        return response()->json([
            'status' => $lineCount
        ]);
    }
}
