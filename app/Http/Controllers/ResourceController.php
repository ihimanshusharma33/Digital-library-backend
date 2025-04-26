<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EBook;
use App\Models\Note;
use App\Models\QuestionPaper;

class ResourceController extends Controller
{
    /**
     * Get resources (e-books, notes, question papers) with filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResources(Request $request)
    {
        try {
            // Get query parameters
            $resourceType = $request->query('type'); // 'ebooks', 'notes', 'question_papers'
            $courseCode = $request->query('course_code');
            $semester = $request->query('semester');

            $results = [];

            // If a specific resource type is requested, only fetch that type
            if ($resourceType) {
                switch (strtolower($resourceType)) {
                    case 'ebooks':
                        $results['ebooks'] = $this->getFilteredEbooks($courseCode, $semester);
                        break;
                    case 'notes':
                        $results['notes'] = $this->getFilteredNotes($courseCode, $semester);
                        break;
                    case 'question_papers':
                        $results['question_papers'] = $this->getFilteredQuestionPapers($courseCode, $semester);
                        break;
                    default:
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid resource type. Valid types are: ebooks, notes, question_papers'
                        ], 400);
                }
            } else {
                // If no specific type is requested, fetch all resource types
                $results = [
                    'ebooks' => $this->getFilteredEbooks($courseCode, $semester),
                    'notes' => $this->getFilteredNotes($courseCode, $semester),
                    'question_papers' => $this->getFilteredQuestionPapers($courseCode, $semester)
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Resources retrieved successfully',
                'data' => $results
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get filtered e-books based on course code and semester
     *
     * @param string|null $courseCode
     * @param int|null $semester
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFilteredEbooks($courseCode, $semester)
    {
        $query = EBook::query();

        if ($courseCode) {
            $query->where('course_code', $courseCode);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }

    /**
     * Get filtered notes based on course code and semester
     *
     * @param string|null $courseCode
     * @param int|null $semester
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFilteredNotes($courseCode, $semester)
    {
        $query = Note::query();

        if ($courseCode) {
            $query->where('course_code', $courseCode);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }

    /**
     * Get filtered question papers based on course code and semester
     *
     * @param string|null $courseCode
     * @param int|null $semester
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFilteredQuestionPapers($courseCode, $semester)
    {
        $query = QuestionPaper::query();

        if ($courseCode) {
            $query->where('course_code', $courseCode);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }
}