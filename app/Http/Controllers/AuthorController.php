<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $authors = Author::with('books')->get();
            return response()->json(['data' => $authors], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching authors', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->validateAuthorData($request);

            $author = Author::create($request->all());

            return response()->json(['message' => 'Author created successfully', 'data' => $author], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating author', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Author $author
     * @return JsonResponse
     */
    public function show(Author $author): JsonResponse
    {
        try {
            $author->load('books');
            return response()->json(['data' => $author], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching author details', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Author $author
     * @return JsonResponse
     */
    public function update(Request $request, Author $author): JsonResponse
    {
        try {
            $this->validateAuthorData($request, $author->id);

            $author->update($request->all());

            return response()->json(['message' => 'Author updated successfully', 'data' => $author], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating author', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Author $author
     * @return JsonResponse
     */
    public function destroy(Author $author): JsonResponse
    {
        try {
            $author->delete();
            return response()->json(['message' => 'Author deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting author', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Validate author data.
     *
     * @param Request $request
     * @param int|null $authorId
     * @throws ValidationException
     */
    private function validateAuthorData(Request $request, ?int $authorId = null): void
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:authors,email',
        ];

        if ($authorId !== null) {
            $rules['email'] .= ',' . $authorId;
        }

        $this->validate($request, $rules);
    }
}
