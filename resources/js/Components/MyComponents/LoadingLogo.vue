<template>
    <div class="flex flex-col items-center text-primary">
        <figure class="mt-20 mx-auto w-[30%] lg:w-[15%]">
            <img class="" src="@/../../public/images/color_logo.png" alt="logo">
        </figure>
        <div class="loader">
            <div class="shape"></div>
        </div>
        <!-- <i class="fa-solid fa-spinner fa-spin text-gray-600 text-4xl mt-2"></i>
        <p>Cargando ...</p> -->
    </div>
</template>

<style>
    /* From Uiverse.io by shadowmurphy */ 
.loader {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  width: 200px;
  height: 15px;
  background-color: rgb(216, 214, 214);
  border-radius: 10px;
  overflow: hidden;
  position: relative;
  margin-top: 15px;
}

.shape {
  width: 50px;
  height: 15px;
  background-image: linear-gradient(144deg, #4fc2c4, #338a8b 50%, #296A6B);
  border-radius: 25px;
  position: absolute;
  animation: slide 1.9s linear infinite;
  background-size: 200%;
}

@keyframes slide {
  0% {
    left: 0;
  }

  50% {
    left: calc(100% - 50px);
  }

  100% {
    left: 0;
  }
}

@keyframes gradientChange {
  0%, 100% {
    background-image: linear-gradient(144deg, #AF40FF, #5B42F3 50%, #00DDEB);
  }

  50% {
    background-image: linear-gradient(144deg, #00DDEB, #5B42F3 50%, #AF40FF);
  }
}
</style>