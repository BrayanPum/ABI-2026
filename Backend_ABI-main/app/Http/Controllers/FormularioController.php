<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function index()
    {
        return view('formulario.index');
    }

    /**
     * Muestra el formulario con todos los departamentos y ciudades de Colombia.
     *
     * @return \Illuminate\View\View
     */
    public function showFormulario()
    {
        // Lista de departamentos y ciudades de Colombia (simulación)
        $departamentos = [
            'Amazonas' => ['Leticia', 'Puerto Nariño'],
            'Antioquia' => ['Medellín', 'Bello', 'Envigado'],
            'Arauca' => ['Arauca', 'Arauquita'],
            'Atlántico' => ['Barranquilla', 'Soledad'],
            'Bogotá D.C.' => ['Bogotá'],
            // Agrega todos los departamentos y sus ciudades aquí
            // Ejemplo: 'Nombre del departamento' => ['Ciudad 1', 'Ciudad 2', ...],
        ];

        return view('cliente.show', compact('departamentos'));
    }

    /**
     * Procesa el formulario y guarda la selección.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardarSeleccion(Request $request)
    {
        // Aquí puedes procesar los datos del formulario enviado
        // Por ejemplo, guardar en la base de datos, etc.
        
        // Redirecciona a donde sea apropiado después de guardar
        return redirect()->route('formulario.index')->with('success', 'Selección guardada correctamente.');
    }
}
