<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class OrgChartController extends Controller
{
    public function index()
    {
        $positions = Position::with('assignments')->get();
        $chartData = [];

        foreach ($positions as $pos) {
            $nodeId = (string) $pos->id;
            $parentId = $pos->parent_id ? (string) $pos->parent_id : '';

            $html = "<div class='org-node'>";
            // Make position title uppercase to match the design
            $html .= "<div class='org-title'>" . strtoupper($pos->name) . "</div>";
            $html .= "<div class='org-slots'>";

            for ($i = 1; $i <= $pos->slots_count; $i++) {
                $assignment = $pos->assignments->firstWhere('slot_index', $i);
                
                if ($assignment && $assignment->employee_name) {
                    $userName = strtoupper($assignment->employee_name);
                    $avatar = $assignment->employee_image ? asset('storage/' . $assignment->employee_image) : asset('images/default-avatar.png'); 
                    
                    $html .= "<div class='org-slot'>";
                    $html .= "<img src='{$avatar}' alt='{$userName}'>";
                    $html .= "<p class='employee-name'>{$userName}</p>";
                    $html .= "</div>";
                } else {
                    $html .= "<div class='org-slot vacant'>";
                    $html .= "<div class='empty-avatar'></div>";
                    $html .= "<p class='employee-name'>VACANT</p>";
                    $html .= "</div>";
                }
            }
            
            $html .= "</div></div>";

            $chartData[] = [
                ['v' => $nodeId, 'f' => $html],
                $parentId,
                $pos->name
            ];
        }

        return view('frontend.org-chart', compact('chartData'));
    }
}