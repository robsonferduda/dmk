<?php

/**
 * This file is part of Filepicker.
 *
 * (c) HazzardWeb <hazzardweb@gmail.com>
 *
 * For the full copyright and license information, please visit:
 * http://codecanyon.net/licenses/standard
 */

namespace Hazzard\Filepicker;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Handler extends BaseHandler
{
    
	public function postAction(Request $request)
    {
    	$files = $request->files->get(
            $this->getParamName(),
            $request->files->get($this->getSingularParamName(), array())
        );

        if (! is_array($files)) {
            $files = array($files);
        }

        $response = array();

        foreach ($files as $file) {
            try {

                $file = $this->upload($file, $request);
                $file->id = $request->id_despesa;
                
            } catch (AbortException $e) {
                $file->errorMessage = $e->getMessage() ?: $this->uploader->getErrorMessage('abort');
            }

            $response[] = $this->fileToArray($file);
        }

        return $this->json($response, 201);
    }
    
}
