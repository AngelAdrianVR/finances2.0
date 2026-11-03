import { onBeforeUnmount, onMounted, ref, unref, watch } from 'vue';

const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * Tweens a number on mount and every time the given source changes.
 * `source` can be a plain number or a computed/ref that resolves to one.
 */
export function useAnimatedNumber(source, { duration = 1200, delay = 0, decimals = 0 } = {}) {
    const value = ref(0);
    let frame = null;

    const easeOutCubic = (progress) => 1 - Math.pow(1 - progress, 3);

    function tweenTo(target, from = 0) {
        if (frame) {
            cancelAnimationFrame(frame);
        }

        if (prefersReducedMotion()) {
            value.value = target;
            return;
        }

        const startedAt = performance.now() + delay;

        const step = (now) => {
            const progress = Math.min(Math.max((now - startedAt) / duration, 0), 1);

            value.value =
                progress >= 1
                    ? target
                    : Number((from + (target - from) * easeOutCubic(progress)).toFixed(decimals));

            if (progress < 1) {
                frame = requestAnimationFrame(step);
            }
        };

        frame = requestAnimationFrame(step);
    }

    onMounted(() => tweenTo(unref(source)));

    watch(
        () => unref(source),
        (next) => tweenTo(next, value.value),
    );

    onBeforeUnmount(() => {
        if (frame) {
            cancelAnimationFrame(frame);
        }
    });

    return value;
}