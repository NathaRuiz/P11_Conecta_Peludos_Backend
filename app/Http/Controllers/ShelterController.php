<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Animal;
use Illuminate\Database\QueryException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ShelterController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $animals = $user->animals;
            return response()->json($animals);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al recuperar los animales: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{   
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'gender' => 'required|in:Macho,Hembra',
            'size' => 'required|in:Pequeño,Mediano,Grande,Gigante',
            'age' => 'required|in:Cachorro,Adulto,Senior',
            'approximate_age' => 'required|string|max:255',
            'status' => 'required|in:Urgente,Disponible,En Acogida,Reservado,Adoptado',
            'my_story' => 'required|string|max:500',
            'description' => 'required|string|max:400',
            'delivery_options' => 'required|string|max:255',
            'image_url' => 'required|image',
            'category_id' => 'required|exists:categories,id',
        ]);
        $user = Auth::user();
        // Registro de información sobre la solicitud recibida
        Log::info('Solicitud recibida en el método store de ShelterController.');

        // Verificar si se proporcionó una imagen válida
        if (!$request->hasFile('image_url') || !$request->file('image_url')->isValid()) {
            throw new \Exception('No se proporcionó una imagen válida.');
        }

        // Subir la imagen a Cloudinary
        $file = $request->file('image_url');
        Log::info('Archivo recibido:', ['filename' => $file->getClientOriginalName()]);
        $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

        // Verificar la carga exitosa
        if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
            throw new \Exception('Error al cargar la imagen a Cloudinary');
        }

        // Crear el animal en la base de datos
        $animal = Animal::create([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'breed' => $request->input('breed'),
            'gender' => $request->input('gender'),
            'size' => $request->input('size'),
            'age' => $request->input('age'),
            'approximate_age' => $request->input('approximate_age'),
            'status' => $request->input('status'),
            'my_story' => $request->input('my_story'),
            'description' => $request->input('description'),
            'delivery_options' => $request->input('delivery_options'),
            'image_url' => $cloudinaryUpload->getSecurePath(),
            'public_id' => $cloudinaryUpload->getPublicId(),
            'user_id' => $user->id
        ]);
       

        // Registro de éxito
        Log::info('Animal guardado correctamente.');

        // Respuesta JSON al cliente
        return response()->json(['message' => 'Animal guardado correctamente', 'animal' => $animal], 201);
    } catch (\Exception $e) {
        // Manejo de excepciones y registro de errores
        Log::error('Error al almacenar el animal: ' . $e->getMessage());

        // Respuesta JSON al cliente con mensaje de error
        return response()->json(['status' => 500, 'message' => 'Error al almacenar animal: ' . $e->getMessage()], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = auth()->user();
            $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();

            if (!$animal) {
                return response()->json(['message' => 'Animal no encontrado'], 404);
            }

            return response()->json($animal);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al buscar animal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
            //     'name' => 'required|string|max:255',
            // 'breed' => 'required|string|max:255',
            // 'gender' => 'required|in:Macho,Hembra',
            // 'size' => 'required|in:Pequeño,Mediano,Grande,Gigante',
            // 'age' => 'required|in:Cachorro,Adulto,Senior',
            // 'approximate_age' => 'required|string|max:255',
            // 'status' => 'required|in:Urgente,Disponible,En Acogida,Reservado,Adoptado',
            // 'my_story' => 'required|string|max:500',
            // 'description' => 'required|string|max:400',
            // 'delivery_options' => 'required|string|max:255',
            // 'image_url' => 'required|image',
            // 'category_id' => 'required|exists:categories,id',
                'name' => 'string|max:255',
                'breed' => 'string|max:255',
                'gender' => 'in:Macho,Hembra',
                'size' => 'in:Pequeño,Mediano,Grande,Gigante',
                'age' => 'in:Cachorro,Adulto,Senior',
                'approximate_age' => 'string|max:255',
                'status' => 'in:Urgente,Disponible,En Acogida,Reservado,Adoptado',
                'my_story' => 'string|max:500',
                'description' => 'string|max:400',
                'delivery_options' => 'string|max:255',
                'image_url' => 'image', // Permitir que la imagen sea opcional
                'category_id' => 'exists:categories,id',
            ]);
            Log::info('Datos recibidos en la solicitud de actualización:', ['data' => $request->all()]);
            $user = Auth::user();
            $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();
            
            if (!$animal) {
                return response()->json(['message' => 'Animal no encontrado'], 404);
            }
            
            // Si se proporciona una nueva imagen, subirla a Cloudinary
            if ($request->hasFile('image_url')) {
                // Eliminar la imagen anterior de Cloudinary
                Cloudinary::destroy($animal->public_id);
    
                $file = $request->file('image_url');
                $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);
    
                if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                    throw new \Exception('Error al cargar la nueva imagen a Cloudinary');
                }
    
                // Actualizar la URL y el ID público de la nueva imagen
                $animal->image_url = $cloudinaryUpload->getSecurePath();
                $animal->public_id = $cloudinaryUpload->getPublicId();
            }
            
            // Actualizar los demás campos del animal
            $animal->update([
                'name' => $request->input('name'),
                'category_id' => $request->input('category_id'),
                'breed' => $request->input('breed'),
                'gender' => $request->input('gender'),
                'size' => $request->input('size'),
                'age' => $request->input('age'),
                'approximate_age' => $request->input('approximate_age'),
                'status' => $request->input('status'),
                'my_story' => $request->input('my_story'),
                'description' => $request->input('description'),
                'delivery_options' => $request->input('delivery_options'),
                // No es necesario actualizar la URL e ID público de la imagen si no se proporciona una nueva
                // 'image_url' => $cloudinaryUpload->getSecurePath(),
                // 'public_id' => $cloudinaryUpload->getPublicId(),
            ]);
            Log::info('Información del animal despues de la actualización:', ['animal' => $animal]);
            return response()->json(['message' => 'Animal actualizado correctamente'], 200);
        } catch (\Exception $e) {
            // Manejo de excepciones y registro de errores
            Log::error('Error al actualizar el animal: ' . $e->getMessage());
    
            // Respuesta JSON al cliente con mensaje de error
            return response()->json(['status' => 500, 'message' => 'Error al actualizar el animal: ' . $e->getMessage()], 500);
        }
    }
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();

            if (!$animal) {
                return response()->json(['message' => 'Animal no encontrado'], 404);
            }
            $animal->delete();

            return response()->json(['message' => 'Animal eliminado correctamente'], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al eliminar animal: ' . $e->getMessage()], 500);
        }
    }
}
