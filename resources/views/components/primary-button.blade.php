<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-[#142259] hover:bg-[#0e1840] active:bg-[#0a112e] border border-transparent rounded font-semibold text-xs text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#142259] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer']) }}>
    {{ $slot }}
</button>
