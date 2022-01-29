<?php

namespace LaraDev\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use LaraDev\Http\Controllers\Controller;
use LaraDev\Mail\Web\Contact;
use LaraDev\Property;

class WebController extends Controller
{
    public function home()
    {
        $propertiesForSale = Property::sale()->available()->limit(3)->get();
        $propertiesForRent = Property::rent()->available()->limit(3)->get();

        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            'Encontre o imóvel dos seus sonhos na melhor e mais completa imobiliária da região dos cocais',
            route('web.home'),
            asset('frontend/assets/images/share.png'),
            true
        );

        return view('web.home', [
            'head' => $head,
            'propertiesForSale' => $propertiesForSale,
            'propertiesForRent' => $propertiesForRent
        ]);
    }

    public function spotlight()
    {
        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            'Confire nossos destaques na melhor e mais completa imobiliária da região dos cocais',
            route('web.spotlight'),
            asset('frontend/assets/images/share.png'),
            true
        );

        return view('web.spotlight', [
            'head' => $head
        ]);
    }

    public function contact()
    {
        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            'Entre em contato com a nossa equipe para encontrar o imóvel dos seus sonhos na melhor na mais completa imobiliária da região dos cocais',
            route('web.contact'),
            asset('frontend/assets/images/share.png'),
            true
        );

        return view('web.contact', [
            'head' => $head,
        ]);
    }

    public function rent()
    {
        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            'Alugue o imóvel dos seus sonhos na melhor e mais completa imobiliária da região dos cocais',
            route('web.rent'),
            asset('frontend/assets/images/share.png'),
            true
        );

        $filter = new FilterController();
        $filter->clearAllData();

        $properties = Property::rent()->available()->get();

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties,
            'type' => 'rent'
        ]);
    }

    public function sale()
    {
        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            'Compre o imóvel dos seus sonhos na melhor e mais completa imobiliária da região dos cocais',
            route('web.sale'),
            asset('frontend/assets/images/share.png'),
            true
        );

        $filter = new FilterController();
        $filter->clearAllData();

        $properties = Property::sale()->available()->get();

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties,
            'type' => 'sale'
        ]);
    }

    public function rentProperty(Request $request)
    {
        $property = Property::where('slug', $request->slug)->first();

        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            $property->headline ?? $property->title,
            route('web.rentProperty', ['property' => str_slug($property->slug)]),
            $property->cover(),
            true
        );

        return view('web.property', [
            'head' => $head,
            'property' => $property,
            'type' => 'rent'
        ]);
    }

    public function saleProperty(Request $request)
    {
        $property = Property::where('slug', $request->slug)->first();

        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            $property->headline ?? $property->title,
            route('web.saleProperty', ['property' => str_slug($property->slug)]),
            $property->cover(),
            true
        );

        return view('web.property', [
            'head' => $head,
            'property' => $property,
            'type' => 'sale'
        ]);
    }

    public function filter()
    {
        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            'Filtre o imóvel dos seus sonhos na melhor e mais completa imobiliária da região dos cocais',
            route('web.filter'),
            asset('frontend/assets/images/share.png'),
            true
        );

        $filter = new FilterController();

        $itemProperties = $filter->createQuery('id');

        foreach ($itemProperties as $property) {
            $properties[] = $property->id;
        }

        if (!empty($properties)) {
            $properties = Property::whereIn('id', $properties)->get();
        } else {
            $properties = Property::all();
        }

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties
        ]);
    }

    public function experience()
    {
        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            'Viva a experiência de encontrar o imóvel dos seus sonhos na melhor e mais completa imobiliária da região dos cocais',
            route('web.experience'),
            asset('frontend/assets/images/share.png'),
            true
        );

        $filter = new FilterController();
        $filter->clearAllData();

        $properties = Property::whereNotNull('experience')->get();

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties
        ]);
    }

    public function experienceCategory(Request $request)
    {
        $filter = new FilterController();
        $filter->clearAllData();

        if ($request->slug == 'cobertura') {
            $properties = Property::where('experience', 'Cobertura')->get();
        } elseif ($request->slug == 'alto-padrao') {
            $properties = Property::where('experience', 'Alto Padrão')->get();
        } elseif ($request->slug == 'de-frente-para-o-mar') {
            $properties = Property::where('experience', 'De frente para o Mar')->get();
        } elseif ($request->slug == 'condominio-fechado') {
            $properties = Property::where('experience', 'Condomínio Fechado')->get();
        } elseif ($request->slug == 'compacto') {
            $properties = Property::where('experience', 'Compacto')->get();
        } elseif ($request->slug == 'lojas-e-salas') {
            $properties = Property::where('experience', 'Lojas e Salas')->get();
        } else {
            $properties = Property::whereNotNull()->get('experience');
        }

        $head = $this->seo->render(
            env('APP_NAME') . ' - Home',
            'Viva a experiência de encontrar o imóvel de ' . $properties[0]->experience . ' dos seus sonhos na melhor e mais completa imobiliária da região dos cocais',
            route('web.experienceCategory', ['category' => str_slug($properties[0]->experience)]),
            asset('frontend/assets/images/share.png'),
            true
        );

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties
        ]);
    }

    public function sendEmail(Request $request)
    {
        $data = [
            'reply_name' => $request->name,
            'reply_email' => $request->email,
            'cell' => $request->cell,
            'message' => $request->message,
        ];

//        Mail::send(new Contact($data));
//        return redirect()->route('web.sendEmailSuccess');

        return new Contact($data);
    }

    public function sendEmailSuccess()
    {
        return view('emails.optin');
    }
}
