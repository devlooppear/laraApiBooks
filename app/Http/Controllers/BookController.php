<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $books = Book::with('author', 'category')->get();
            return response()->json(['data' => $books], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching books', 'error' => $e->getMessage()], 500);
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
            $this->validateBookData($request);

            $bookData = $request->only(['title', 'author_id', 'category_id']);
            $book = Book::create($bookData);

            $book->load('author', 'category');

            return response()->json(['message' => 'Book created successfully', 'data' => $book], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating book', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function show(Book $book): JsonResponse
    {
        try {
            $book->load('author', 'category');
            return response()->json(['data' => $book], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching book details', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Book $book
     * @return JsonResponse
     */
    public function update(Request $request, Book $book): JsonResponse
    {
        try {
            $validatedData = $this->validateBookData($request);

            $book->update($validatedData);
            $book->categories()->sync($request->input('category_ids'));

            $book->load('author', 'category');

            return response()->json(['message' => 'Book updated successfully', 'data' => $book], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating book', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function destroy(Book $book): JsonResponse
    {
        try {
            $book->delete();
            return response()->json(['message' => 'Book deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting book', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Validate book data.
     *
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    private function validateBookData(Request $request): array
    {
        return $this->validate($request, [
            'title' => 'required',
            'author_id' => 'required|integer|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
        ]);
    }
}
