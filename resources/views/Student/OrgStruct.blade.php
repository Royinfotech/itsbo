<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizational Chart</title>
    <link rel="stylesheet" href="{{ asset('assets/css/Org.css') }}">
</head>
<body>
    <div class="org-chart">
        @if($currentSchoolYear)
            <h2>School Year: {{ $currentSchoolYear->year }} - {{ $currentSchoolYear->semester }} ITSBO Organizational Structure</h2>
        @endif

        <div class="chart-container">
            @if(!empty($positions))
                @foreach($positions as $position)
                    <div class="position">
                        @php
                            $positionKey = strtolower(str_replace(' ', '', $position));
                            $officer = $officers[$positionKey] ?? null;
                            $defaultImage = 'assets/pictures/' . $position . '.png';
                        @endphp
                        
                        <img src="{{ $officer && $officer->image_path ? asset('storage/' . $officer->image_path) : asset($defaultImage) }}" 
                             alt="{{ $position }}">
                        <p class="name">
                            {{ $officer ? $officer->first_name . ' ' . $officer->last_name : $position . '\'s Name' }}
                        </p>
                        <p class="title">{{ $position }}</p>
                    </div>
                    
                    @if($loop->iteration % 3 == 0)
                        </div><div class="row">
                    @endif
                @endforeach
            @else
                <div class="position">
                    <p>No positions defined for the current school year</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
