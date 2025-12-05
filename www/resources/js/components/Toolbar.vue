<script setup lang="ts">
import { Icon, Button, Slider } from '.';


</script>

<template>
  <div
    class="flex-none left-0 w-full bg-white dark:bg-black backdrop-blur-md text-black dark:text-white p-3 flex items-center justify-between"
  >
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 bg-gray-700 rounded"></div>
      <div>
        <p class="font-semibold text-sm">Song Title</p>
        <p class="text-xs text-gray-600 dark:text-gray-300">Artist Name</p>
      </div>
      <Icon name="heart" class="size-6" />
    </div>

    <div class="flex flex-col items-center w-168 space-y-1">
      <div class="flex items-center gap-4">
        <Button><Icon name="arrows-shuffle" class="size-5" /></Button>
        <Button><Icon name="skip-back" class="size-6" /></Button>
        <Button class="w-12 h-12 !bg-cyan-500 rounded-full flex items-center justify-center">
          <Icon name="play" class="size-6 text-white" />
        </Button>
        <Button><Icon name="skip-forward" class="size-6" /></Button>
        <Button><Icon name="repeat" class="size-5" /></Button>
      </div>

      <div class="flex items-center gap-2 w-full text-xs text-gray-600 dark:text-gray-300">
        <span>1:15</span>
        <Slider></Slider>
        <span>3:45</span>
      </div>
    </div>

    <div class="flex items-center gap-4">
      <Icon name="microphone-2" class="size-5" />
      <Icon name="list" class="size-5" />
      <div class="flex items-center gap-2">
        <Icon name="volume" class="size-5" />
        <Slider></Slider>
      </div>
    </div>
  </div>
</template>
