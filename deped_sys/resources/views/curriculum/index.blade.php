@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');

    .curriculum-wrapper { max-width: 1000px; margin: 0 auto; padding: 20px; }
    .dynamic-banner { width: 100%; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    
    .dynamic-content-box {
        background: #fdfdfd;
        border: 1px solid #e0e0e0;
        padding: 30px;
        border-radius: 8px;
        font-family: 'Cinzel', serif;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .learning-materials-section { padding-top: 10px; }
    .strand-title { color: #2c3e50; font-weight: bold; margin-top: 20px; }
    
    .pdf-link { display: inline-flex; align-items: center; color: #d9534f; text-decoration: none; margin-bottom: 8px; font-family: sans-serif; }
    .pdf-link:hover { text-decoration: underline; }

    .external-link-cards { display: flex; gap: 20px; margin-top: 40px; flex-wrap: wrap; }
    .card-link { flex: 1; min-width: 200px; padding: 20px; background: #003366; color: white; text-align: center; text-decoration: none; border-radius: 8px; font-weight: bold; }
    .card-link:hover { background: #002244; color: white; }
</style>

<div class="curriculum-wrapper">

    {{-- Element 1: Dynamic Image --}}
    @if($pageData && $pageData->banner_image_path)
        <img src="{{ asset('storage/' . $pageData->banner_image_path) }}" alt="Curriculum Banner" class="dynamic-banner">
    @endif

    {{-- Element 2: Blox Box (Learning Strands and PDFs) --}}
    <div class="dynamic-content-box">
        
        <div class="learning-materials-section">
            <h3 style="font-weight: bold; font-size: 1.5rem; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
                Learning Materials
            </h3>

            @forelse($strands as $strand)
                <div class="strand-block">
                    <h4 class="strand-title">{{ $strand->name }}</h4>
                    
                    @if($strand->materials->count() > 0)
                        <ul style="list-style-type: none; padding-left: 0;">
                            @foreach($strand->materials as $material)
                                <li>
                                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="pdf-link">
                                        📄 {{ $material->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p style="color: #777; font-size: 0.9em; font-family: sans-serif;">No materials available yet.</p>
                    @endif
                </div>
            @empty
                <p style="font-family: sans-serif;">No learning strands configured.</p>
            @endforelse
        </div>
    </div>

    {{-- Element 3: The Hardcoded External Links --}}
    <div class="external-link-cards">
        <a href="https://www.deped.gov.ph/k-to-12/about/k-to-12-basic-education-curriculum/academic-track/" target="_blank" class="card-link">
            Academic Track
        </a>
        <a href="https://www.deped.gov.ph/k-to-12/about/k-to-12-basic-education-curriculum/academic-track/" target="_blank" class="card-link">
            Technical-Vocational-Livelihood Track
        </a>
        <a href="https://www.deped.gov.ph/k-to-12/about/k-to-12-basic-education-curriculum/academic-track/" target="_blank" class="card-link">
            Sports Track
        </a>
        <a href="https://www.deped.gov.ph/k-to-12/about/k-to-12-basic-education-curriculum/academic-track/" target="_blank" class="card-link">
            Arts and Design Track
        </a>
    </div>

</div>
@endsection