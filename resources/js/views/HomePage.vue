<template>
    <div
        class="d-flex justify-content-center flex-column align-items-center mt-2"
    >
        <h1>Guess the number game</h1>
    </div>
    <div>
        <div class="all-inputs-container border m-2 p-2 px-5 mx-4">
            <div class="input-group mt-3">
                <!-- <label for="name">Your name</label> -->
                <span class="input-group-text" id="basic-addon1"
                    ><i class="fa-solid fa-user"></i
                ></span>
                <input
                    name="name"
                    type="text"
                    class="name form-control p-2"
                    v-model="gameData.name"
                    placeholder="username"
                />
            </div>
            <div class="input-group mt-4">
                <span class="input-group-text" id="basic-addon1"
                    ><i class="fa-regular fa-square-minus"></i
                ></span>
                <input
                    name="minimum"
                    type="number"
                    class="min form-control p-2"
                    v-model="gameData.minNumber"
                    placeholder="minimum"
                />
            </div>
            <div class="input-group mt-4">
                <span class="input-group-text" id="basic-addon1"
                    ><i class="fa-regular fa-square-plus"></i
                ></span>
                <input
                    name="maximum"
                    type="number"
                    class="max form-control p-2"
                    v-model="gameData.maxNumber"
                    placeholder="maximum"
                />
            </div>
            <div class="input-group mt-4">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-stopwatch"></i>
                </span>
                <input
                    name="tries"
                    type="number"
                    class="tries form-control p-2"
                    v-model="gameData.maxTries"
                    placeholder="max tries"
                />
            </div>
            <div class="input-group mt-4">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-hashtag"></i>
                </span>
                <input
                    name="seconds"
                    type="number"
                    class="seconds form-control p-2"
                    v-model="gameData.maxSeconds"
                    placeholder="max seconds"
                />
            </div>
            <button class="btn btn-primary mt-4" @click="startGame">
                Start game
            </button>
        </div>
    </div>
    <table class="m-2 mt-4 px-5 mx-4">
        <thead>
            <tr class="theader">
                <th class="px-3 py-1">#</th>
                <th class="px-3 py-1">place</th>
                <th class="px-3 py-1">Name</th>
                <th class="px-3 py-1">Minimum</th>
                <th class="px-3 py-1">Maximum</th>
                <th class="px-3 py-1">Max seconds</th>
                <th class="px-3 py-1">Max tries</th>
                <th class="px-3 py-1">Num guesses</th>
                <th class="px-3 py-1">Time to beat</th>
                <th class="px-3 py-1">Answer</th>
                <th class="px-3 py-1">Play date</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(user, index) in users" :key="index">
                <td class="px-3">{{ user.id }}</td>
                <td class="px-3">{{ index + 1 }}</td>
                <td class="px-3">{{ user.name }}</td>
                <td class="px-3">{{ user.minimum }}</td>
                <td class="px-3">{{ user.maximum }}</td>
                <td class="px-3">{{ user.seconds }}</td>
                <td class="px-3">{{ user.tries }}</td>
                <td class="px-3">{{ user.guesses }}</td>
                <td class="px-3">{{ user.seconds_to_beat }}</td>
                <td class="px-3">{{ user.answer }}</td>
                <td class="px-3">{{ user.play_date }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
import router from "../router";
import { useToast } from "vue-toastification";
import gameDataApi from "../api/gamedata";
export default {
    data() {
        return {
            gameData: {
                name: "",
                minNumber: null,
                maxNumber: null,
                maxTries: null,
                maxSeconds: null,
            },
            users: [],
        };
    },

    mounted() {
        // removes modal backdrop on a new page
        if (document.querySelector(".modal-backdrop") != null) {
            document.querySelector(".modal-backdrop").remove();
        }

        this.getUsers();
    },

    methods: {
        // before starting the game, validates all inputs
        startGame() {
            const toast = useToast();
            const nameVal = $(".name").val().trim();
            const minVal = parseInt($(".min").val());
            const maxVal = parseInt($(".max").val());
            const triesVal = parseInt($(".tries").val());
            const secondsVal = parseInt($(".seconds").val());

            if (isNaN(minVal) || isNaN(maxVal) || isNaN(triesVal) || isNaN(secondsVal) || nameVal === "") {
                toast.error("Please fill in all fields with valid values.");
                return;
            }

            if (minVal < 0) {
                toast.error("Minimum can't be less than 0.");
                return;
            }

            if (maxVal <= minVal) {
                toast.error("Maximum must be greater than minimum.");
                return;
            }

            if (triesVal <= 0 || secondsVal <= 0) {
                toast.error("The tries and/or seconds can't be 0.");
                return;
            }

            // if all is correct sets data in db and pushes the user to the correct route
            gameDataApi.setUser(this.gameData);
            router.push("/game");
        },

        // gets all users and displays them in a table
        getUsers() {
            gameDataApi.getUsers().then((res) => {
                for (const key in res.data.users) {
                    // Check if the key is an actual property of the object 
                    if (Object.hasOwnProperty.call(res.data.users, key)) {
                        const element = res.data.users[key];
                        this.users.push(element);
                    }
                }
            });
        }
    },
};
</script>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td,
th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-of-type(even) {
    background-color: #dddddd;
}

.theader {
    background-color: #dddddd;
}
</style>
