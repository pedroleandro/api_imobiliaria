<?php

namespace LaraDev\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LaraDev\Http\Controllers\Controller;

class FilterController extends Controller
{
    public function search(Request $request)
    {
        session()->remove('category');
        session()->remove('type');
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        if ($request->search === 'sale') {
            session()->put('sale', true);
            session()->remove('rent');
            $properties = $this->createQuery('category');
        }

        if ($request->search === 'rent') {
            session()->put('rent', true);
            session()->remove('sale');
            $properties = $this->createQuery('category');
        }

        if ($properties->count()) {
            foreach ($properties as $property) {
                $category[] = $property->category;
            }

            $collect = collect($category);
            $data = $collect->unique()->toArray();

            return response()->json($this->setResponse('success', $data));
        }

        return response()->json($this->setResponse('fail', [], 'Whooooops! Não foi retornado nenhum dado para essa pesquisa.'));
    }

    public function category(Request $request)
    {
        session()->remove('type');
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('category', $request->search);
        $properties = $this->createQuery('type');

        if ($properties->count()) {
            foreach ($properties as $property) {
                $type[] = $property->type;
            }

            $collect = collect($type);
            $data = $collect->unique()->toArray();

            return response()->json($this->setResponse('success', $data));
        }

        return response()->json($this->setResponse('fail', [], 'Whooooops! Não foi retornado nenhum dado para essa pesquisa.'));
    }

    public function type(Request $request)
    {
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('type', $request->search);
        $properties = $this->createQuery('neighborhood');

        if ($properties->count()) {
            foreach ($properties as $property) {
                $neighborhood[] = $property->neighborhood;
            }

            $collect = collect($neighborhood);
            $data = $collect->unique()->toArray();

            return response()->json($this->setResponse('success', $data));
        }

        return response()->json($this->setResponse('fail', [], 'Whooooops! Não foi retornado nenhum dado para essa pesquisa.'));
    }

    public function neighborhood(Request $request)
    {
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('neighborhood', $request->search);
        $properties = $this->createQuery('bedrooms');

        if ($properties->count()) {
            foreach ($properties as $property) {

                if ($property->bedrooms === 0 || $property->bedrooms === 1) {
                    $bedrooms[] = $property->bedrooms . ' quarto';
                } else {
                    $bedrooms[] = $property->bedrooms . ' quartos';
                }
            }

            $bedrooms[] = "Indiferente";

            $collect = collect($bedrooms)->unique()->toArray();
            sort($collect);
            $data = $collect;

            return response()->json($this->setResponse('success', $data));
        }

        return response()->json($this->setResponse('fail', [], 'Whooooops! Não foi retornado nenhum dado para essa pesquisa.'));
    }

    public function bedrooms(Request $request)
    {
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('bedrooms', $request->search);
        $properties = $this->createQuery('suites');

        if ($properties->count()) {
            foreach ($properties as $property) {

                if ($property->suites === 0 || $property->suites === 1) {
                    $suites[] = $property->suites . ' suite';
                } else {
                    $suites[] = $property->suites . ' suites';
                }
            }

            $suites[] = "Indiferente";

            $collect = collect($suites)->unique()->toArray();
            sort($collect);
            $data = $collect;

            return response()->json($this->setResponse('success', $data));
        }

        return response()->json($this->setResponse('fail', [], 'Whooooops! Não foi retornado nenhum dado para essa pesquisa.'));
    }

    public function suites(Request $request)
    {
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('suites', $request->search);
        $properties = $this->createQuery('bathrooms');

        if ($properties->count()) {
            foreach ($properties as $property) {

                if ($property->bathrooms === 0 || $property->bathrooms === 1) {
                    $bathrooms[] = $property->bathrooms . ' banheiro';
                } else {
                    $bathrooms[] = $property->bathrooms . ' banheiros';
                }
            }

            $bathrooms[] = "Indiferente";

            $collect = collect($bathrooms)->unique()->toArray();
            sort($collect);
            $data = $collect;

            return response()->json($this->setResponse('success', $data));
        }

        return response()->json($this->setResponse('fail', [], 'Whooooops! Não foi retornado nenhum dado para essa pesquisa.'));
    }

    public function bathrooms(Request $request)
    {
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('bathrooms', $request->search);
        $properties = $this->createQuery('garage,garage_covered');

        if ($properties->count()) {
            foreach ($properties as $property) {

                $property->garage = $property->garage + $property->garage_covered;

                if ($property->garage === 0 || $property->garage === 1) {
                    $garage[] = $property->garage . ' garagens';
                } else {
                    $garage[] = $property->garage . ' garagens';
                }
            }

            $garage[] = "Indiferente";

            $collect = collect($garage)->unique()->toArray();
            sort($collect);
            $data = $collect;

            return response()->json($this->setResponse('success', $data));
        }

        return response()->json($this->setResponse('fail', [], 'Whooooops! Não foi retornado nenhum dado para essa pesquisa.'));
    }

    public function garage(Request $request)
    {
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('garage', $request->search);

        if (session('sale') === true) {
            $properties = $this->createQuery('sale_price as price');
        } else {
            $properties = $this->createQuery('rent_price as price');
        }

        if ($properties->count()) {
            foreach ($properties as $property) {

                $price[] = 'A partir de R$ ' . number_format($property->price, 2, ',', '.');

            }

            $collect = collect($price)->unique()->toArray();
            sort($collect);
            $data = $collect;

            return response()->json($this->setResponse('success', $data));
        }

        return response()->json($this->setResponse('fail', [], 'Whooooops! Não foi retornado nenhum dado para essa pesquisa.'));
    }

    public function priceBase(Request $request)
    {
        session()->remove('price_limit');

        session()->put('price_base', $request->search);

        if (session('sale') === true) {
            $properties = $this->createQuery('sale_price as price');
        } else {
            $properties = $this->createQuery('rent_price as price');
        }

        if ($properties->count()) {
            foreach ($properties as $property) {

                $price[] = 'Até R$ ' . number_format($property->price, 2, ',', '.');

            }

            $collect = collect($price)->unique()->toArray();
            sort($collect);
            $data = $collect;

            return response()->json($this->setResponse('success', $data));
        }

        return response()->json($this->setResponse('fail', [], 'Whooooops! Não foi retornado nenhum dado para essa pesquisa.'));
    }

    public function priceLimit(Request $request)
    {
        session()->put('price_limit', $request->search);
        return response()->json($this->setResponse('success', []));
    }

    public function createQuery($field)
    {
        $sale = session('sale');
        $rent = session('rent');
        $category = session('category');
        $type = session('type');
        $neighborhood = session('neighborhood');
        $bedrooms = session('bedrooms');
        $suites = session('suites');
        $bathrooms = session('bathrooms');
        $garage = session('garage');
        $priceBase = session('price_base');
        $priceLimit = session('price_limit');

        return DB::table('properties')
            ->when($sale, function ($query, $sale) {
                return $query->where('sale', $sale);
            })
            ->when($rent, function ($query, $rent) {
                return $query->where('rent', $rent);
            })
            ->when($category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($type, function ($query, $type) {
                return $query->whereIn('type', $type);
            })
            ->when($neighborhood, function ($query, $neighborhood) {
                return $query->whereIn('neighborhood', $neighborhood);
            })
            ->when($bedrooms, function ($query, $bedrooms) {
                if ($bedrooms == 'Indiferente') {
                    return $query;
                }
                $bedrooms = (int)$bedrooms;
                return $query->where('bedrooms', $bedrooms);
            })
            ->when($suites, function ($query, $suites) {
                if ($suites == 'Indiferente') {
                    return $query;
                }
                $suites = (int)$suites;
                return $query->where('suites', $suites);
            })
            ->when($bathrooms, function ($query, $bathrooms) {
                if ($bathrooms == 'Indiferente') {
                    return $query;
                }
                $bathrooms = (int)$bathrooms;
                return $query->where('bathrooms', $bathrooms);
            })
            ->when($garage, function ($query, $garage) {
                if ($garage == 'Indiferente') {
                    return $query;
                }
                $garage = (int)$garage;
                return $query->whereRaw('garage + garage_covered = ? OR garage = ? OR garage_covered = ?', [$garage, $garage, $garage]);
            })
            ->when($priceBase, function ($query, $priceBase) {
                $priceBase = (float)str_replace(',', '.', str_replace('.', '', explode('R$ ', $priceBase, 2)[1]));

                if ($priceBase == 'Indiferente') {
                    return $query;
                }

                if (session('sale') == true) {
                    return $query->where('sale_price', '>=', $priceBase);
                } else {
                    return $query->where('rent_price', '>=', $priceBase);
                }
            })
            ->when($priceLimit, function ($query, $priceLimit) {
                $priceLimit = (float)str_replace(',', '.', str_replace('.', '', explode('R$ ', $priceLimit, 2)[1]));

                if ($priceLimit == 'Indiferente') {
                    return $query;
                }

                if (session('sale') == true) {
                    return $query->where('sale_price', '<=', $priceLimit);
                } else {
                    return $query->where('rent_price', '<=', $priceLimit);
                }
            })
            ->get(explode(',', $field));
    }

    public function clearAllData()
    {
        session()->remove('sale');
        session()->remove('rent');
        session()->remove('category');
        session()->remove('type');
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');
    }

    private function setResponse(string $status, array $data = null, string $message = null)
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
    }
}
