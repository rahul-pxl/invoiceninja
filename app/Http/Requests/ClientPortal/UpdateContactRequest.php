<?php
/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2019. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://opensource.org/licenses/AAL
 */

namespace App\Http\Requests\ClientPortal;

use App\Http\Requests\Request;
use App\Utils\Traits\MakesHash;
use Zend\Diactoros\Response\JsonResponse;

class UpdateContactRequest extends Request
{
    use MakesHash;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize() : bool
    {
        return $this->encodePrimaryKey(auth()->user()->id) === request()->segment(3);
    }

    public function rules()
    {

        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:client_contacts,email,' . auth()->user()->id,
            'password' => 'sometimes|nullable|min:6|confirmed',
        ];

    }

    

}
