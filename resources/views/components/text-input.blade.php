@props(['disabled' => false, 'error' => false])

<input @disabled($disabled) {{ $attributes->merge([
    'class' => 'w-full px-3 py-2 text-xs font-medium rounded border transition-colors placeholder:text-slate-400 placeholder:font-normal ' . 
    ($error 
        ? 'border-rose-400 text-rose-900 dark:text-rose-300 focus:border-rose-500 focus:outline-none' 
        : 'border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white bg-white dark:bg-[#0f1b40] focus:border-[#142259] dark:focus:border-slate-500 focus:outline-none') . 
    ' disabled:bg-slate-50 disabled:text-slate-400 disabled:border-slate-200 disabled:cursor-not-allowed'
]) }}>
