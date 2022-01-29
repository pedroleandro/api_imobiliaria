<?php

namespace LaraDev\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use LaraDev\Http\Controllers\Controller;
use LaraDev\Http\Requests\Admin\PropertyRequest;
use LaraDev\Property;
use LaraDev\PropertyImage;
use LaraDev\Support\Cropper;
use LaraDev\User;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $properties = Property::orderBy('id', 'DESC')->get();

        return view('admin.properties.index', [
            'properties' => $properties
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('admin.properties.create', [
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PropertyRequest $request)
    {
        $newProperty = Property::create($request->all());

        $newProperty->setSlug();

        if (!empty($request->only('files')['files'])) {
            $validator = Validator::make($request->only('files')['files'], ['files.*' => 'image']);

            if ($validator->fails() === true) {
                return redirect()->back()->withInput()->with([
                    'color' => 'orange',
                    'message' => 'Todas as imagens devem ser do tipo jpg, jpeg, png, gif ou svg.',
                ]);
            }
        }

        if ($request->allFiles()) {

            foreach ($request->allFiles()['files'] as $image) {
                $newImageForThisNewProperty = new PropertyImage();
                $newImageForThisNewProperty->property = $newProperty->id;
                $newImageForThisNewProperty->path = $image->storeAs('properties/' . $newProperty->id, str_slug($request->title) . '-' . str_replace('.', '', microtime(true)) . '.' . $image->extension());
                $newImageForThisNewProperty->save();
                unset($newImageForThisNewProperty);
            }

        }

        return redirect()->route('admin.properties.edit', [
            'property' => $newProperty->id
        ])->with(['color' => 'green', 'message' => 'Imóvel cadastrado com sucesso']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $property = Property::where('id', $id)->first();
        $users = User::orderBy('name')->get();

        return view('admin.properties.edit', [
            'property' => $property,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PropertyRequest $request, $id)
    {
        $property = Property::where('id', $id)->first();

        $property->setSaleAttribute($request->sale);
        $property->setRentAttribute($request->rent);
        $property->setAirConditioningAttribute($request->air_conditioning);
        $property->setBarAttribute($request->bar);
        $property->setLibraryAttribute($request->library);
        $property->setBarbecueGrillAttribute($request->barbecue_grill);
        $property->setAmericanKitchenAttribute($request->american_kitchen);
        $property->setFittedKitchenAttribute($request->fitted_kitchen);
        $property->setPantryAttribute($request->pantry);
        $property->setEdiculeAttribute($request->edicule);
        $property->setOfficeAttribute($request->office);
        $property->setBathtubAttribute($request->bathtub);
        $property->setFireplaceAttribute($request->fireplace);
        $property->setLavatoryAttribute($request->lavatory);
        $property->setFurnishedAttribute($request->furnished);
        $property->setPoolAttribute($request->pool);
        $property->setSteamRoomAttribute($request->steam_room);
        $property->setViewOfTheSeaAttribute($request->view_of_the_sea);

        $property->fill($request->all());

        if (!$property->save()) {
            return redirect()->back()->withInput()->withErrors();
        }

        $property->setSlug();

        if (!empty($request->only('files')['files'])) {
            $validator = Validator::make($request->only('files')['files'], ['files.*' => 'image']);

            if ($validator->fails() === true) {
                return redirect()->back()->withInput()->with([
                    'color' => 'orange',
                    'message' => 'Todas as imagens devem ser do tipo jpg, jpeg, png, gif ou svg.',
                ]);
            }
        }

        if ($request->allFiles()) {

            foreach ($request->allFiles()['files'] as $image) {
                $newImageForThisProperty = new PropertyImage();
                $newImageForThisProperty->property = $property->id;
                $newImageForThisProperty->path = $image->storeAs('properties/' . $property->id, str_slug($request->title) . '-' . str_replace('.', '', microtime(true)) . '.' . $image->extension());
                $newImageForThisProperty->save();
                unset($newImageForThisProperty);
            }

        }

        return redirect()->route('admin.properties.edit', [
            'user' => $property->id
        ])->with(['color' => 'green', 'message' => 'Imóvel atualizado com sucesso']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function imageSetCover(Request $request)
    {
        $imageSetCover = PropertyImage::where('id', $request->image)->first();

        $allImages = PropertyImage::where('property', $imageSetCover->property)->get();
        foreach ($allImages as $image) {
            $image->cover = false;
            $image->save();
        }

        $imageSetCover->cover = true;
        $imageSetCover->save();

        $json = [
            'success' => true
        ];

        return response()->json($json);

    }

    public function imageRemove(Request $request)
    {
        $imageDelete = PropertyImage::where('id', $request->image)->first();

        Storage::delete($imageDelete->path);
        Cropper::flush($imageDelete->path);
        $imageDelete->delete();

        $json = [
            'success' => true
        ];

        return response()->json($json);
    }
}
