<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApiControllerV1 extends Controller
{
    public function __construct()
    {
        $this->data = null;
        $this->messages = [];
        $this->error = false;
        $this->code = Response::HTTP_OK;
    }

    public function callFunction($func)
    {
        DB::beginTransaction();
        try {
            $func($this);
            if (!count($this->messages)) {
                array_push($this->messages, "Success");
            }

            DB::commit();
        } catch (AuthenticationException $e) {
            DB::rollBack();

            $this->error = true;
            array_push($this->messages, $e->getMessage());
            $this->code = Response::HTTP_UNAUTHORIZED;
        } catch (ValidationException $e) {
            DB::rollBack();

            $this->error = true;
            foreach ($e->errors() as $errors) {
                foreach ($errors as $key => $error) {
                    array_push($this->messages, $errors[$key]);
                }
            }
            $this->code = $e->status;
        } catch (QueryException $e) {
            DB::rollBack();
            report($e);

            $this->error = true;
            array_push($this->messages, $e->errorInfo[2]);
            $this->code = Response::HTTP_INTERNAL_SERVER_ERROR;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            $this->error = true;
            array_push($this->messages, $e->getMessage());
            $this->code = Response::HTTP_NOT_FOUND;
        } catch (CustomException $e) {
            DB::rollBack();

            $this->error = true;
            array_push($this->messages, $e->getMessage());
            $this->code = $e->getCode();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);

            $this->error = true;
            array_push($this->messages, $e->getMessage());
            $this->code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response(
            [
                "data" => $this->data,
                "messages" => $this->messages,
                "error" => $this->error,
            ],
            $this->code
        );
    }
}
