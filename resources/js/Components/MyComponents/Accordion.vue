<template>
    <div>
        <div @click.prevent="toggleAccordion()" :aria-expanded="accordionOpen" :aria-controls="`accordion-text-${id}`"
            :title="title" class="flex items-center space-x-3" :class="{'justify-center': position == 'center', 'justify-end': position == 'end'}">
            <slot name="trigger">
                <!-- Default Trigger Button -->
                <button :id="`accordion-title-${id}`"
                    class="w-full text-start px-2 my-2 flex items-center justify-between text-xs rounded-md py-2 ml-2"
                    :class="active ? 'bg-[#272829] text-white' : 'hover:text-white hover:bg-[#272829] text-[#999999]'">
                    <span v-html="icon"></span>
                    <p class="truncate text-sm"> {{ title }}</p>
                    <i class="fa-solid fa-angle-down transform origin-center transition duration-200 ease-out text-xs text-[#999999]"
                        :class="{ '!rotate-180': accordionOpen }">
                    </i>
                </button>
            </slot>
        </div>
        <div :id="`accordion-text-${id}`" role="region" :aria-labelledby="`accordion-title-${id}`"
            class="grid text-sm overflow-hidden transition-all duration-300 ease-in-out"
            :class="accordionOpen ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
            <div class="overflow-hidden">
                <p class="pb-1">
                    <slot name="content" />
                </p>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Accordion',
    data() {
        return {
            accordionOpen: this.active,
        }
    },
    props: {
        title: String,
        id: Number,
        active: Boolean,
        icon: String,
        position:{
            type: String,
            default: 'end',
        }
    },
    components: {
    },
    methods: {
        toggleAccordion() {
            this.accordionOpen = !this.accordionOpen;
        }
    }
}
</script>
