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
            $courseId = $request->query('course_id');
            $semester = $request->query('semester');

            $results = [];

            // If a specific resource type is requested, only fetch that type
            if ($resourceType) {
                switch (strtolower($resourceType)) {
                    case 'ebooks':
                        $results['ebooks'] = $this->getFilteredEbooks($courseId, $semester);
                        break;
                    case 'notes':
                        $results['notes'] = $this->getFilteredNotes($courseId, $semester);
                        break;
                    case 'question_papers':
                        $results['question_papers'] = $this->getFilteredQuestionPapers($courseId, $semester);
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
                    'ebooks' => $this->getFilteredEbooks($courseId, $semester),
                    'notes' => $this->getFilteredNotes($courseId, $semester),
                    'question_papers' => $this->getFilteredQuestionPapers($courseId, $semester)
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
     * @param string|null $courseId
     * @param int|null $semester
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFilteredEbooks($courseId, $semester)
    {
        $query = EBook::query();

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }

    /**
     * Get filtered notes based on course code and semester
     *
     * @param string|null $courseId
     * @param int|null $semester
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFilteredNotes($courseId, $semester)
    {
        $query = Note::query();

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }

    /**
     * Get filtered question papers based on course code and semester
     *
     * @param string|null $courseId
     * @param int|null $semester
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFilteredQuestionPapers($courseId, $semester)
    {
        $query = QuestionPaper::query();

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }
}