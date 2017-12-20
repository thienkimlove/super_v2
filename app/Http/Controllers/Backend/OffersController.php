<?php namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Offer;
use Illuminate\Http\Request;


class OffersController extends Controller
{

    public function index()
    {
        return view('offers.index');
    }

    public function create()
    {
        return view('offers.create');
    }

    public function store(OfferRequest $request)
    {
        $request->store();

        flash()->success('Success!', 'Offer successfully created.');

        return redirect()->route('offers.index');
    }

    public function edit($id)
    {
        $offer = Offer::find($id);

        return view('offers.edit', compact('offer'));
    }

    public function update(OfferRequest $request, $id)
    {
        $request->save($id);

        flash()->success('Thành công', 'Cập nhật thành công!');

        return redirect()->route('offers.edit', $id);
    }

    public function dataTables(Request $request)
    {
        return Offer::getDataTables($request);
    }

}
