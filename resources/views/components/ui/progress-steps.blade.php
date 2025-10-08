@props(['steps', 'currentStep'])

<div class="bg-white dark:bg-navy-800 rounded-lg border border-gray-200 dark:border-navy-700 p-6">
    <nav aria-label="Progress">
        <ol role="list" class="flex items-center justify-between">
            @foreach($steps as $index => $step)
                @php
                    $stepNumber = $index + 1;
                    $isCompleted = $stepNumber < $currentStep;
                    $isCurrent = $stepNumber === $currentStep;
                @endphp

                <li class="relative flex-1 {{ $loop->last ? '' : 'pr-8 sm:pr-20' }}">
                    @if(!$loop->last)
                        <!-- Connector Line -->
                        <div class="absolute top-4 left-0 -ml-px mt-0.5 h-0.5 w-full {{ $isCompleted ? 'bg-primary-600' : 'bg-gray-300 dark:bg-navy-600' }}" aria-hidden="true"></div>
                    @endif

                    <div class="group relative flex items-start">
                        <span class="flex h-9 items-center">
                            @if($isCompleted)
                                <span class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-primary-600">
                                    <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            @elseif($isCurrent)
                                <span class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-primary-600 bg-white dark:bg-navy-800">
                                    <span class="h-2.5 w-2.5 rounded-full bg-primary-600"></span>
                                </span>
                            @else
                                <span class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300 dark:border-navy-600 bg-white dark:bg-navy-800">
                                    <span class="text-sm font-medium text-gray-500 dark:text-navy-300">{{ $stepNumber }}</span>
                                </span>
                            @endif
                        </span>
                        <span class="ml-4 flex min-w-0 flex-col">
                            <span class="text-sm font-medium {{ $isCurrent ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-navy-300' }}">
                                {{ $step['title'] }}
                            </span>
                            @if(isset($step['description']))
                                <span class="text-xs text-gray-500 dark:text-navy-400">{{ $step['description'] }}</span>
                            @endif
                        </span>
                    </div>
                </li>
            @endforeach
        </ol>
    </nav>
</div>

