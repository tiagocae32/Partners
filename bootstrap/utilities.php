<?php

use Illuminate\Support\Facades\DB;


/**
 * Function para comenzar una transaccion
 */
function startTransaction()
{
    DB::beginTransaction();
}

/**
 * Function para realizar un rollback ante cualquier inconveniente
 */
function rollback($nivel = null)
{
    DB::rollback($nivel);
}

/**
 * Function para realizar el commit y finalizar la transaccion
 */
function commit()
{
    DB::commit();
}