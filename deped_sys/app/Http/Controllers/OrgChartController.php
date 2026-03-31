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
            $html .= "<div class='org-title'>" . strtoupper($pos->name) . "</div>";
            $html .= "<div class='org-slots'>";

            for ($i = 1; $i <= $pos->slots_count; $i++) {
                $assignment = $pos->assignments->firstWhere('slot_index', $i);
                
                if ($assignment && $assignment->employee_name) {
                    $userName = strtoupper($assignment->employee_name);
                    $avatar = $assignment->employee_image ? asset('storage/' . $assignment->employee_image) : asset('images/default-avatar.png'); 
                    
                    $html .= "<div class='org-slot'>";
                    
                    // BIG EDGE-TO-EDGE PHOTO
                    $html .= "<img src='{$avatar}' class='employee-photo-hero' alt='{$userName}'>";
                    
                    // DETAILS SECTION BELOW PHOTO
                    $html .= "<div class='details-container'>";
                    $html .= "<p class='employee-name-bold'>{$userName}</p>";
                    // Optional: You can output the role name again here, or comment it out since it's in the dark title bar
                    $html .= "<p class='employee-position-line'>" . strtoupper($pos->name) . "</p>";
                    
                    // Info Lines
                    $html .= "<div class='employee-info-lines'>";
                    $html .= "<div class='info-line'><i class='fas fa-building'></i><p><span class='label'>DepEd Office</span></p></div>";
                    // Uncomment/edit these if you add email or phone to your database later!
                    // $html .= "<div class='info-line'><i class='fas fa-envelope'></i><p><span class='label'>email@deped.gov.ph</span></p></div>";
                    // $html .= "<div class='info-line'><i class='fas fa-phone'></i><p><span class='label'>09123456789</span></p></div>";
                    $html .= "</div>"; 
                    
                    $html .= "</div>"; // End details-container
                    $html .= "</div>"; // End org-slot
                    
                } else {
                    $html .= "<div class='org-slot vacant'>";
                    
                    $html .= "<div class='empty-photo-hero'><i class='fas fa-user-circle'></i></div>";
                    
                    $html .= "<div class='details-container'>";
                    $html .= "<p class='employee-name-bold'>VACANT</p>";
                    $html .= "<p class='employee-position-line'>" . strtoupper($pos->name) . "</p>";
                    $html .= "<div class='employee-info-lines'>";
                    $html .= "<div class='info-line'><i class='fas fa-building'></i><p><span class='label'>Position Available</span></p></div>";
                    $html .= "</div>";
                    $html .= "</div>";
                    
                    $html .= "</div>"; // End org-slot
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