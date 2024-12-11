<template>
    <div class="flex flex-col items-center text-primary">
        <figure class="mt-20 mx-auto w-[25%] lg:w-[15%]">
            <img class="" src="@/../../public/images/color_logo.png" alt="logo">
        </figure>
        <div class="loader mt-3">
            <div class="circle"></div>
        </div>
    </div>
</template>

<style>
/* From Uiverse.io by mrhyddenn */ 
.loader {
  background: transparent;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
}

.circle {
  width: 80px;
  height: 80px;
  border: 2px solid #ccc;
  position: absolute;
  border-radius: 50%;
  animation: circle14512 2s linear infinite;
}

.circle::before {
  content: '';
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #296A6B;
  box-shadow: 0 0 20px #9ae932, 0 0 60px #9ae932;
}

@keyframes circle14512 {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>