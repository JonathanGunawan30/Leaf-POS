<template>
    <div
        id="app"
        class="relative w-screen h-screen bg-black overflow-hidden transition-all duration-500 ease-in-out"
        :class="{ 'opacity-0 translate-y-8': isLeaving }"
    >
        <vue-particles
            id="tsparticles"
            class="absolute inset-0 z-0"
            :options="particlesOptions"
        />

        <div
            class="relative z-20 flex flex-col items-center justify-center min-h-screen px-8 text-center"
        >
            <!-- Logo area -->
            <div
                class="flex items-center gap-4 mb-16 transform transition-all duration-1000 ease-out delay-300"
                :class="{
              'translate-y-0 opacity-100': isLoaded,
              'translate-y-8 opacity-0': !isLoaded,
            }"
            >
                <div class="relative w-12 h-12">
                    <img
                        src="/images/leafPos1.svg"
                        alt="Logo"
                        class="w-full h-full object-contain transition-all duration-300 hover:scale-110"
                    />
                </div>
                <span class="text-4xl font-semibold text-white tracking-tight">404</span>
            </div>

            <h1
                class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white leading-tight mb-4 tracking-tight transition-all duration-1000 ease-out delay-500"
                :class="{
              'translate-y-0 opacity-100': isLoaded,
              'translate-y-8 opacity-0': !isLoaded,
            }"
            >
                Oops! Halaman<br />tidak ditemukan
            </h1>

            <p
                class="text-base sm:text-lg md:text-xl text-white/70 font-normal mb-10 transition-all duration-1000 ease-out delay-700"
                :class="{
              'translate-y-0 opacity-100': isLoaded,
              'translate-y-8 opacity-0': !isLoaded,
            }"
            >
                Sepertinya kamu nyasar... tapi kami bantu arahkan kembali!
            </p>

            <div
                class="flex flex-col sm:flex-row gap-4 mb-20 transition-all duration-1000 ease-out delay-900"
                :class="{
              'translate-y-0 opacity-100': isLoaded,
              'translate-y-8 opacity-0': !isLoaded,
            }"
            >
                <a
                    href="/"
                    class="inline-flex items-center justify-center px-8 py-3 rounded-full bg-white text-black text-base font-semibold hover:bg-gray-200 transition-all duration-300 hover:-translate-y-1 shadow-lg"
                >
                    Kembali ke Beranda
                </a>

                <button
                    @click="goBack"
                    class="inline-flex items-center justify-center px-8 py-3 rounded-full border-2 border-neon-green text-neon-green bg-transparent text-base font-semibold hover:bg-neon-green/10 transition-all duration-300 hover:-translate-y-1 shadow-lg"
                >
                    Halaman Sebelumnya
                </button>
            </div>

            <div
                class="text-xl md:text-2xl lg:text-3xl font-semibold text-white leading-snug tracking-tight transition-all duration-1000 ease-out delay-1100"
                :class="{
              'translate-y-0 opacity-100': isLoaded,
              'translate-y-8 opacity-0': !isLoaded,
            }"
            >
                <p class="mb-2">
                    Tidak ada yang bisa ditemukan di sini,
                    <span class="text-neon-green hover:text-neon-green-light transition-all duration-300"
                    >tapi jangan panik</span
                    >
                </p>
                <p>— mari kita kembali ke jalur yang benar</p>
            </div>
        </div>

        <!-- Among Us Characters -->
        <div class="among-us-container">
            <div
                v-for="char in amongUsChars"
                :key="char.id"
                class="among-us-char"
                :style="{
                    left: char.x + 'px',
                    bottom: char.bottomOffset + 'px',
                    width: char.size + 'px',
                    height: 'auto',
                    transform: `scaleX(${char.facingLeft ? -1 : 1}) ${char.isJumping ? 'translateY(-10px)' : ''}`,
                    transition: char.isJumping ? 'transform 0.3s ease-in-out' : 'transform 0.1s ease-out',
                    zIndex: 15 - char.id
                }"
            >
                <img
                    :src="char.image"
                    class="w-full h-auto"
                    :class="{ 'animate-bounce': char.isJumping }"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import redImage from '../../../assets/amongus/red.png';
import blueImage from '../../../assets/amongus/blue.png';
import cyanImage from '../../../assets/amongus/cyan.png';
import pinkImage from '../../../assets/amongus/pink.png';
import purpleImage from '../../../assets/amongus/purple.png';
import yellowImage from '../../../assets/amongus/yellow.png';
import greenImage from '../../../assets/amongus/green.png';
import whiteImage from '../../../assets/amongus/white.png';

const isLoaded = ref(false);
const isLeaving = ref(false);
const amongUsChars = ref([]);
const animationFrame = ref(null);
const colorImages = {
    red: redImage,
    blue: blueImage,
    cyan: cyanImage,
    pink: pinkImage,
    purple: purpleImage,
    yellow: yellowImage,
    green: greenImage,
    white: whiteImage,
};

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 200);

    generateAmongUsChars();
    startAnimation();
});

onUnmounted(() => {
    if (animationFrame.value) {
        cancelAnimationFrame(animationFrame.value);
    }
});

const goBack = () => {
    isLeaving.value = true;
    setTimeout(() => {
        window.history.back();
    }, 500);
};

const particlesOptions = {
    background: {
        color: "#000000"
    },
    fpsLimit: 60,
    interactivity: {
        events: {
            onClick: { enable: true, mode: 'push' },
            onHover: { enable: true, mode: 'repulse' }
        },
        modes: {
            push: { quantity: 4 },
            repulse: { distance: 200, duration: 0.4 }
        }
    },
    particles: {
        number: {
            value: 250,
            density: { enable: true, area: 800 }
        },
        color: { value: "#39FF14" },
        links: {
            enable: true,
            color: "#39FF14",
            distance: 150,
            opacity: 0.5,
            width: 1
        },
        move: { enable: true, speed: 2 },
        size: { value: 2 },
        opacity: { value: 0.5 },
        shape: { type: "circle" }
    },
    detectRetina: true
};

const colors = ['cyan', 'red', 'blue', 'pink', 'yellow', 'purple', 'green', 'white'];

function generateAmongUsChars() {
    const count = colors.length;
    const screenWidth = window.innerWidth;

    amongUsChars.value = Array.from({ length: count }, (_, i) => {
        const color = colors[i];
        return {
            id: i,
            x: Math.random() * (screenWidth - 80),
            y: 0,
            vx: (Math.random() - 0.5) * 3,
            vy: 0,
            size: Math.floor(Math.random() * 70 + 70),
            bottomOffset: Math.floor(Math.random() * 30 + 10),
            color,
            image: colorImages[color],
            facingLeft: Math.random() > 0.5,
            isJumping: false,
            jumpCooldown: 0,
            idleTime: Math.random() * 120,
            directionChangeTime: Math.random() * 180 + 120,
            currentDirectionTime: 0,
            targetX: null,
            isWandering: true
        };
    });
}

function startAnimation() {
    const animate = () => {
        updateCharacters();
        animationFrame.value = requestAnimationFrame(animate);
    };
    animate();
}

function updateCharacters() {
    const screenWidth = window.innerWidth;

    amongUsChars.value.forEach(char => {
        char.currentDirectionTime++;
        char.jumpCooldown = Math.max(0, char.jumpCooldown - 1);

        if (char.currentDirectionTime >= char.directionChangeTime) {
            char.currentDirectionTime = 0;
            char.directionChangeTime = Math.random() * 180 + 120;

            if (Math.random() < 0.3) {
                char.vx = 0;
                char.idleTime = Math.random() * 60 + 30;
            } else {
                char.vx = (Math.random() - 0.5) * 4;
                char.facingLeft = char.vx > 0 ? false : true;
            }
        }

        if (char.idleTime > 0) {
            char.idleTime--;
            char.vx = 0;
        }

        if (char.jumpCooldown === 0 && Math.random() < 0.005) {
            char.isJumping = true;
            char.jumpCooldown = 60;

            setTimeout(() => {
                char.isJumping = false;
            }, 300);
        }


        char.x += char.vx;

        if (char.x <= 0) {
            char.x = 0;
            char.vx = Math.random() * 2 + 0.5;
            char.facingLeft = false;
        } else if (char.x >= screenWidth - char.size) {
            char.x = screenWidth - char.size;
            char.vx = -(Math.random() * 2 + 0.5);
            char.facingLeft = true;
        }

        if (Math.random() < 0.002) {
            char.idleTime = Math.random() * 90 + 30;
        }

        if (Math.random() < 0.001) {
            char.vx = -char.vx;
            char.facingLeft = !char.facingLeft;
        }
    });
}
</script>

<style scoped>
.among-us-container {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 400px;
    z-index: 15;
    pointer-events: none;
    overflow: hidden;
}

@keyframes wiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-2deg); }
    75% { transform: rotate(2deg); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
}

.among-us-char {
    position: absolute;
    will-change: transform, left, bottom;
    transition: transform 0.2s ease-out;
}

.among-us-char:hover {
    animation: wiggle 0.5s ease-in-out;
}

.among-us-char img {
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
    transition: filter 0.3s ease;
}

.among-us-char.animate-bounce img {
    filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.6));
}

.text-neon-green {
    color: #39FF14;
}

.border-neon-green {
    border-color: #39FF14;
}

.hover\:bg-neon-green\/10:hover {
    background-color: rgba(57, 255, 20, 0.1);
}

.text-neon-green-light {
    color: #7FFF7F;
}
</style>
