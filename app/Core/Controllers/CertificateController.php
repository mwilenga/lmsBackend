<?php

namespace App\Core\Controllers;

use App\Core\Services\CertificateService;
use App\Core\DTO\JsonResponse;
use App\Core\Enum\FormMethod;
use App\Http\Resources\CertificateResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateController
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function ilist(Request $request)
    {
        $returnData = [];

        $q = $request->q;
        $name = $request->name;
        $l = $request->l;
        $paginate = $request->paginate;
        $per_page = $request->per_page;
        $company_id = $request->company_id;
        $user_id = $request->user_id;
        try {
            $extra = [ 'q' => $q, 'company_id' => $company_id, 'user_id' => $user_id];
            if($paginate) {
                $extra['paginate'] = $paginate;
                $extra['per_page'] = $per_page;
            }
            $listOfItem = $this->certificateService->search(null, $name, $l, $extra);
            if($listOfItem->isNotEmpty()) {
                $resource = CertificateResource::collection($listOfItem)->response()->getData(true);
                $returnData = [ 'list_of_item' => $resource ];
            }

        return JsonResponse::get(JsonResponse::$OK, 'List of Item', $returnData);

        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }
        
    }

    public function iget(Request $request)
    {
        $returnData = [];
        $id = $request->id;
        try {
            $item = $this->certificateService->one($id, null, [ 'with_company_logo' => 'yes' ]);

            if(!empty($item)) {
                $returnData = [ 'item' => new CertificateResource($item) ];
            }

            return JsonResponse::get(JsonResponse::$OK, 'Item', $returnData);
        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }

    }

    public function formAction(Request $request)
    {
        $output = JsonResponse::get(JsonResponse::$ERROR, 'Ooops something went wrong !!');
        $rules = $this->certificateService->validationRules();

        try {
            $formMethod = $request->form_method;

            if (in_array($formMethod, [FormMethod::get('UPDATE/value'), FormMethod::get('SAVE/value')])) {
                $error = Validator::make($request->all(), $rules);

                if ($error->fails()) {
                    $output = JsonResponse::get(JsonResponse::$ERROR, $error->errors()->all());
                    return response()->json($output);
                }
            }

            $id = $request->id;
            $currentUser = auth()->user();
            $request['active_user'] = $currentUser->id;

            switch ($formMethod) {
                case FormMethod::get('UPDATE/value') :

                    $output = $this->certificateService->transaction(function () use ($id, &$request) {
                        $certificate = $this->certificateService->get($id);

                        if ($certificate) {
                             $documentUrl = null;
                                if ($request->certificate) {
                                    $folderPath = "uploads/certificate/";
                                    if (!is_dir($folderPath)) {
                                        @mkdir($folderPath, 0775, true);
                                    }
                                    
                                    $base64Document = explode(";base64,", $request->certificate);
                                    $explodeDocument = explode("application/", $base64Document[0]);
                                    $documentType = $explodeDocument[1];
                                    $document_base64 = base64_decode($base64Document[1]);
                                    $file = $folderPath . uniqid() . '.' . $documentType;
                                    
                                    if (!file_put_contents($file, $document_base64)) {
                                        return response()->json(JsonResponse::get(JsonResponse::$ERROR, "Failed to save document. Please try again."));
                                    }
                                    $documentUrl = $file;   
                                }
                                $request['path'] = $documentUrl;

                            $certificate = $this->certificateService->update($request, $id);
                            return JsonResponse::get(JsonResponse::$OK, "Certificate upload updated successful", new CertificateResource($certificate));
                        }

                        return JsonResponse::get(JsonResponse::$ERROR, "The data you're trying to update could not be found on the server!");
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('SAVE/value') :

                    $output = $this->certificateService->transaction(function () use ($request) {
                        $request['uuid'] = Str::uuid();
                        $documentUrl = null;
                        if ($request->certificate) {
                            $folderPath = "uploads/certificate/";
                            if (!is_dir($folderPath)) {
                                @mkdir($folderPath, 0775, true);
                            }
                            
                            $base64Document = explode(";base64,", $request->certificate);
                            $explodeDocument = explode("application/", $base64Document[0]);
                            $documentType = $explodeDocument[1];
                            $document_base64 = base64_decode($base64Document[1]);
                            $file = $folderPath . uniqid() . '.' . $documentType;
                            
                            if (!file_put_contents($file, $document_base64)) {
                                return response()->json(JsonResponse::get(JsonResponse::$ERROR, "Failed to save document. Please try again."));
                            }
                            $documentUrl = $file;   
                        }
                        $request['path'] = $documentUrl;

                        $certificate = $this->certificateService->save($request);

                        return JsonResponse::get(JsonResponse::$OK, "Certificate uploaded successfully", new CertificateResource($certificate));
                    });

                    return response()->json($output);
                    break;

                case FormMethod::get('DELETE/value') :

                    $output = $this->certificateService->transaction(function () use ($id) {
                        $certificate = $this->certificateService->get($id);
                        if ($certificate) {
                            $output = $this->certificateService->delete($id);
                            return JsonResponse::get(JsonResponse::$OK, "Certificate deleted successfully", $output);
                        }
                        return JsonResponse::get(JsonResponse::$ERROR, "The data you're trying to delete could not be found on the server!");
                    });

                    return response()->json($output);
                    break;

                default:
                    return response()->json($output);
                    break;
            }

        } catch (\Exception $e) {
            return response()->json(JsonResponse::get(JsonResponse::$ERROR, $e->getMessage()));
        }
    }

}