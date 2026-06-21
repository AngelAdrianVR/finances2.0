<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-[#151A1A]">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-gray-100 dark:bg-[#1E2424] overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
