<template>
    <div class="page-container m-3 d-flex flex-column">
        <h1>Guess the number!</h1>
        <div class="guess-container d-flex flex-column">
            <label for="guess">Your guess</label>
            <input
                name="guess"
                type="number"
                pattern="[0-9]"
                class="guess-input form-control"
                :min="gameData.minimum || 1"
                :max="gameData.maximum || 100"
                @keydown.enter="guess"
                v-model="guessedNumber"
            />
            <div class="min-max d-flex flex-column mt-1">
                <span>Min: {{ gameData.minimum }}</span>
                <span>Max: {{ gameData.maximum }}</span>
            </div>
            <span>Guesses left: {{ gameData.tries }}</span>
            <span class="mt-2">Previous guess: {{ this.previousGuess }}</span>
        </div>
        <div class="timer d-flex flex-column mt-3">
            <span class="timer">time elapsed: {{ timeElapsed }}s</span>
            <span class="timer">time left: {{ gameData.seconds }}s</span>
        </div>
        <button class="quit-btn btn btn-danger mt-4" @click="goBack()">
            Quit & exit
        </button>
    </div>
    <WonGameModal></WonGameModal>
    <LostGameModal></LostGameModal>
</template>

<script>
import router from "../router";
import gamedataApi from "../api/gamedata";
import { useToast } from "vue-toastification";
import LostGameModal from "../components/LostGameModal.vue";
import WonGameModal from "../components/WonGameModal.vue";
let gametimer;
export default {
    data() {
        return {
            gameData: {},
            guessedNumber: 0,
            playable: true,
            timeElapsed: 0,
            previousGuess: 0,
            triesToBeat: 0,
        };
    },

    components: {
        LostGameModal,
        WonGameModal,
    },

    created() {
        gamedataApi.getSession().then((res) => {
            if (!res.data.signed_in) {
                router.push("/");
            } else if (res.data.userData) {
                this.gameData = res.data.userData;   
                // Check if the game timer && time elapsed values are present in local storage
                const gameTimer = parseInt(localStorage.getItem('gameTimer'));
                const elapsedTime = parseInt(localStorage.getItem('timeElapsed'));
                console.log(gameTimer, elapsedTime);
                if (!isNaN(gameTimer) && !isNaN(elapsedTime)) {
                    // Set values to the stored values
                    this.gameData.seconds = gameTimer;
                    this.timeElapsed = elapsedTime;
                }
                localStorage.clear();   
            }
        });
    },

    mounted() {
        // only accepts numbers
        let numberInputs = document.querySelectorAll('input[type="number"]');
        numberInputs.forEach((input) => {
            input.addEventListener("keypress", (e) => {
                let value = Math.max(
                    Math.min(
                        parseInt(input.value.trim()),
                        this.gameData.maximum || 100
                    ),
                    this.gameData.minimum || 1
                );
            });
        });

        // handles timer logic
        gametimer = setInterval(() => {
            this.gameData.seconds -= 1;
            this.timeElapsed += 1;
            if (this.gameData.seconds === 0 ) {
                clearInterval(gametimer);
                $(".lostmodal").modal("show");
                gamedataApi.clearSession();
            }
        }, 1000);
    
        // sets timer before refreshing/leaving page
         window.addEventListener('beforeunload', (event) => {
            // Save the current game timer value in local storage
            localStorage.setItem('gameTimer', this.gameData.seconds);
            localStorage.setItem('timeElapsed', this.timeElapsed);
            // Cancel the default event action to show the confirm dialog box
            event.preventDefault();
        });
    },

    beforeUnmount(){
        localStorage.clear();
    },

    methods: {
        guess() {
            const toast = useToast();
            if (
                this.guessedNumber < this.gameData.minimum ||
                this.guessedNumber > this.gameData.maximum
            ) {
                this.playable = false;
                toast.warning("Please don't exceed the min/maximum");
            } else {
                this.playable = true;
            }
            if (this.playable) {
                this.gameData.tries -= 1;
                this.triesToBeat += 1;
                if (this.gameData.tries === 0) {
                    $(".lostmodal").modal("show");
                } else {
                    gamedataApi.guess(this.guessedNumber).then((res) => {
                        if (res.data.msg === "Correct") {
                            clearInterval(gametimer);
                            $(".wonmodal").modal("show");
                            let obj = {
                                guesses: this.triesToBeat,
                                seconds: this.timeElapsed,
                            };
                            gamedataApi.sendGuess(obj);
                        } else if (res.data.msg === "Lower") {
                            toast.info(res.data.msg);
                        } else {
                            toast.info(res.data.msg);
                        }
                    });
                    this.previousGuess = this.guessedNumber;
                }
            }
        },

        goBack() {
            gamedataApi.clearSession();
            this.$router.go(-1);
        },

        clearTimer() {
            gamedataApi.updateTimer({ time: this.gameData.seconds });
        }
    },
};
</script>

<style>
.guess-input {
    width: 15rem !important;
}

.quit-btn {
    width: 7rem !important;
}

.timer {
    font-size: 2rem;
}
</style>
