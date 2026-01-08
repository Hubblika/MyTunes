<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { Button, Icon, Slider } from '@/components/common';

const audio = ref<HTMLAudioElement | null>(null);
const audioSrc = 'audio/default.mp3';

const isPlaying = ref(false);
const shuffle = ref(false);
const loop = ref(false);
const liked = ref(false);
const duration = ref(0);

const time = ref(30);
const volume = ref(20);
let previousVolume = 0;

function playButtonClick() {
    if (!audio.value) return;

    if (isPlaying.value) {
        audio.value.pause();
        isPlaying.value = false;
    } else {
        audio.value.play();
        isPlaying.value = true;
    }
}

function shufflebuttonClick() {
    shuffle.value = !shuffle.value;
    //TODO: shuffle the music in the queue
}

function loopbuttonClick() {
    loop.value = !loop.value;
    //TODO: change looping
}

function skipForward() {
    //TODO: skip to the next music
}

function skipBack() {
    //TODO: skip to the beginning of the music
}

function like() {
    console.log(liked.value);
    liked.value = !liked.value;
}

function openQueue() {
    //TODO: open the queue
    console.log('open q');
}

function openLyrics() {
    //TODO:open lyrics
    console.log('open lyrics');
}

const formattedTime = computed(() => {
    const minutes = Math.floor(time.value / 60);
    const seconds = time.value % 60;

    return `${minutes}:${seconds.toString().padStart(2, '0')}`
});

const formattedDuration = computed(() => {
    const minutes = Math.floor(duration.value / 60);
    const seconds = duration.value % 60;

    return `${minutes}:${seconds.toString().padStart(2, '0')}`
});


const volumeIcon = computed(() => {
    if (volume.value === 0) return 'volume-3';
    if (volume.value < 50) return 'volume-2';
    return 'volume';
});

function mute() {
    if (volume.value === 0) {
        volume.value = previousVolume;
    }
    else {
        const temp = volume.value;
        volume.value = 0;
        previousVolume = temp;
    }
}

watch(time, (newTime) => {
    if (audio.value && Math.abs(audio.value.currentTime - newTime) > 1) {
        audio.value.currentTime = newTime;
    }
});

watch(volume, (newVolume) => {
    if (audio.value) {
        audio.value.volume = newVolume / 100;
    }
});

onMounted(() => {
    if (!audio.value) return;

    time.value = 0;

    audio.value.addEventListener('timeupdate', () => {
        time.value = Math.floor(audio.value!.currentTime);
    });

    audio.value.addEventListener('loadedmetadata', () => {
        duration.value = Math.floor(audio.value!.duration);
    });

    audio.value.addEventListener('ended', () => {
        if(!loop.value) {
            isPlaying.value = false;
            time.value = 0;
        }
        else {
            time.value = 0;
            audio.value!.currentTime = 0;
            audio.value!.play();
        }
    });
});
</script>

<template>
    <audio ref="audio" :src="audioSrc"></audio>
    <div
        class="flex-none left-0 w-full bg-white dark:bg-black backdrop-blur-md text-black dark:text-white p-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gray-700 rounded"></div>
            <div>
                <p class="font-semibold text-sm hover:underline hover:cursor-pointer">{{ $t('toolbar.songTitle') }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-300 hover:underline hover:cursor-pointer">{{
                    $t('toolbar.artistName') }}</p>
            </div>
            <Button @click="like" class="group transition-all duration-150">
                <Icon :name="liked ? 'heart-filled' : 'heart'"
                    :class="['size-6 transition-colors duration-150', liked ? 'text-pink-500 group-hover:text-pink-400' : 'text-black dark:text-white group-hover:text-black/60 dark:group-hover:text-white/80']" />
            </Button>
        </div>

        <div class="flex flex-col items-center w-2xl space-y-1">
            <div class="flex items-center gap-4">
                <Button @click="shufflebuttonClick" class="group transition-all duration-150">
                    <Icon name="arrows-shuffle"
                        class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80"
                        :class="shuffle ? 'text-cyan-500!' : 'text-black dark:text-white'" />
                </Button>
                <Button @click="skipBack" class="group transition-all duration-150">
                    <Icon name="skip-back"
                        class="size-6 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
                </Button>
                <Button @click="playButtonClick"
                    class="w-12 h-12 rounded-full bg-cyan-500 flex items-center justify-center transition-all duration-150 hover:bg-cyan-400">
                    <Icon :name="isPlaying ? 'player-pause-filled' : 'player-play-filled'" class="size-6 text-white" />
                </Button>
                <Button @click="skipForward" class="group transition-all duration-150">
                    <Icon name="skip-forward"
                        class="size-6 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
                </Button>
                <Button @click="loopbuttonClick" class="group transition-all duration-150">
                    <Icon name="repeat"
                        class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80"
                        :class="loop ? 'text-cyan-500!' : 'text-black dark:text-white'" />
                </Button>
            </div>

            <div class="flex items-center gap-2 w-full text-xs text-gray-600 dark:text-gray-300">
                <span class="hover:cursor-default">{{ formattedTime }}</span>
                <Slider v-model="time" :max="duration"></Slider>
                <span class="hover:cursor-default">{{ formattedDuration }}</span>
            </div>
        </div>

        <div class="flex items-center">
            <Button @click="openLyrics" class="px-2! group transition-all duration-150">
                <Icon name="microphone-2"
                    class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
            </Button>
            <Button @click="openQueue" class="px-2! group transition-all duration-150">
                <Icon name="list"
                    class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
            </Button>
            <div class="flex items-center gap-2">
                <Button @click="mute" class="px-2! group transition-all duration-150">
                    <Icon :name="volumeIcon"
                        class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
                </Button>
                <Slider v-model="volume" :max="100"></Slider>
            </div>
        </div>
    </div>
</template>
