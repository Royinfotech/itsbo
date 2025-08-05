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
                        {{ $officer ? $officer->first_name . ' ' . $officer->last_name : 'Vacant' }}
                    </p>
                    <p class="title">{{ $position }}</p>
                </div>
            @endforeach
        @else
            <div class="position">
                <p>No positions defined for the current school year</p>
            </div>
        @endif
    </div>
</div>