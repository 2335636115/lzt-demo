<style>
	@import "/static/mediate/css/personal-stars.css";
</style>
<template>
	<view>
		<view class="starsicon-list">
			<image @click="btnStars1" class="starsicon" :src="starsObject[0]" mode="widthFix"></image>
			<image @click="btnStars2" class="starsicon" :src="starsObject[1]" mode="widthFix"></image>
			<image @click="btnStars3" class="starsicon" :src="starsObject[2]" mode="widthFix"></image>
			<image @click="btnStars4" class="starsicon" :src="starsObject[3]" mode="widthFix"></image>
			<image @click="btnStars5" class="starsicon" :src="starsObject[4]" mode="widthFix"></image>
		</view>
		<view class="starsicon-results" v-if="starsIndex==1">非常不满意</view>
		<view class="starsicon-results" v-if="starsIndex==2">一般</view>
		<view class="starsicon-results" v-if="starsIndex==3">良好</view>
		<view class="starsicon-results" v-if="starsIndex==4">满意</view>
		<view class="starsicon-results" v-if="starsIndex==5">非常满意</view>
	</view>
</template>

<script>
	export default {
		name: "StarsRating",
		data() {
			return {

			};
		},
		props: {
			starsIndex: {}, // 星级评价分数
			isEditStars: {}, // 是否编辑星级评价分数
			starsObject: {} // 数组
		},
		methods: {
			btnStars1: function() {
				var _this = this
				if (_this.isEditStars) {
					_this.$emit("starsClick", 1)
				}
			},
			btnStars2: function() {
				var _this = this
				if (_this.isEditStars) {
					_this.$emit("starsClick", 2)
				}
			},
			btnStars3: function() {
				var _this = this
				if (_this.isEditStars) {
					_this.$emit("starsClick", 3)
				}
			},
			btnStars4: function() {
				var _this = this
				if (_this.isEditStars) {
					_this.$emit("starsClick", 4)
				}
			},
			btnStars5: function() {
				var _this = this
				if (_this.isEditStars) {
					_this.$emit("starsClick", 5)
				}
			}

		}

	}
</script>
