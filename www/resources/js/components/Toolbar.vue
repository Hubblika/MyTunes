<script setup lang="ts">
import { Button, Icon, Slider } from '@/components/common';
import { ref, computed, onMounted } from "vue";

const isPlaying = ref(false);
const shuffle = ref(false);
const loop = ref(false);
const liked = ref(false);

const time = ref(30);
const volume = ref(20);
let previousVolume = 0;

function playButtonClick() {
  isPlaying.value = !isPlaying.value;
  //TODO: start the music player;
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


const volumeIcon = computed(() => {
  if (volume.value === 0) return 'volume-3';
  if (volume.value < 50) return 'volume-2';
  return 'volume';
});

function mute() {
    if(volume.value === 0) {
        volume.value = previousVolume;
    }
    else {
        let temp = volume.value;
        volume.value = 0;
        previousVolume = temp;
    }
}

onMounted
</script>

<template>
    <div class="flex-none left-0 w-full bg-white dark:bg-black backdrop-blur-md text-black dark:text-white p-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gray-700 rounded"></div>
            <div>
                <p class="font-semibold text-sm">Song Title</p>
                <p class="text-xs text-gray-600 dark:text-gray-300">Artist Name</p>
            </div>
            <Button @click="like"><Icon :name="liked ? 'heart-filled' : 'heart'" class="size-6" /></Button>
        </div>

        <div class="flex flex-col items-center w-168 space-y-1">
            <div class="flex items-center gap-4">
                <Button @click="shufflebuttonClick"><Icon name="arrows-shuffle" class="size-5" :class="shuffle ? 'text-cyan-500' : 'text-black'"/></Button>
                <Button @click="skipBack"><Icon name="skip-back" class="size-6" /></Button>
                <Button @click="playButtonClick" class="w-12 h-12 !bg-cyan-500 rounded-full flex items-center justify-center">
                    <Icon :name="isPlaying ? 'player-pause-filled' : 'player-play-filled'" class="size-6 text-white"/>
                </Button>
                <Button @click="skipForward"><Icon name="skip-forward" class="size-6" /></Button>
                <Button @click="loopbuttonClick"><Icon name="repeat" class="size-5" :class="loop ? 'text-cyan-500' : 'text-black'"/></Button>
            </div>

            <div class="flex items-center gap-2 w-full text-xs text-gray-600 dark:text-gray-300">
                <span>{{ formattedTime }}</span>
                <Slider v-model="time"></Slider>
                <span>3:45</span>
            </div>
        </div>

        <div class="flex items-center">
            <Button @click="openLyrics" class="px-2!"><Icon name="microphone-2" class="size-5"/></Button>
            <Button @click="openQueue" class="px-2!"><Icon name="list" class="size-5"/></Button>
            <div class="flex items-center gap-2">
            <Button @click="mute" class="px-2!"><Icon :name="volumeIcon" class="size-5"/></Button>
            <Slider v-model="volume"></Slider>
            </div>
        </div>
    </div>
</template>
