<style>
	@import "/static/mediate/css/mediator-detail.css";
</style>
<template>
	<view>
		<view class="box-bg head-title">
			<uni-nav-bar color="white" backgroundColor="#ec6618" left-icon="" right-icon="" title="" />
		</view>
		<view class="orange-back">
			<image class="orange-back-image" src="/static/mediate/images/b-006.png"></image>
			<view class="orange-back-introduce">
				<view class="introduce-img">
					<image class="introduce-image" :src="image"></image>
				</view>
				<view class="introduce-list">
					<view class="introduce-list-title">姓名：</view>
					<view class="introduce-list-content">{{grade}}</view>
				</view>
				<view class="introduce-list">
					<view class="introduce-list-title">职务：</view>
					<view class="introduce-list-content">{{grade}}</view>
				</view>
				<view class="introduce-list">
					<view class="introduce-list-title">机构：</view>
					<view class="introduce-list-content">{{organ}}</view>
				</view>
				<view class="introduce-list">
					<view class="introduce-list-title">执业年限：</view>
					<view class="introduce-list-content">{{term}}</view>
				</view>
				<view class="introduce-list">
					<view class="introduce-list-title">擅长领域：</view>
					<view class="introduce-list-content">{{good_at}}</view>
				</view>
			</view>
			<view class="introduce-personal">
				<view class="personal-title">
					<image class="personal-image-Introduction" src="/static/mediate/images/f-002.png"></image>
					<view class="personal-text">个人简介</view>
				</view>
				<view class="personal-content">
					<rich-text :nodes="description"></rich-text>
				</view>
			</view>
			<view class="introduce-personal">
				<view class="personal-title">
					<image class="personal-image-honor" src="/static/mediate/images/f-003.png"></image>
					<view class="personal-text">个人荣誉</view>
				</view>
				<view class="personal-content">
					<rich-text :nodes="honor"></rich-text>
				</view>
			</view>
		</view>
		<myFoot></myFoot>
	</view>
</template>

<script>
	import myFoot from "./footer.vue"

	export default {
		components: {
			myFoot,
		},
		data() {
			return {
				image: '',
				username: '',
				grade: '',
				organ: '',
				term: 0,
				good_at: '',
				phone: '13968685756',
				description: '',
				honor: ''
			}
		},
		onShow() {
			this.getdetail();
		},
		methods: {
			getdetail() {
				var _this = this;
				let local = location.href;
				let id = this.getParam(local, "id");
				this.$http.get('mediate/people_detail', {
					id: id
				}, result => {
					_this.image = result.data.image;
					_this.username = result.data.username;
					_this.grade = result.data.grade.name;
					_this.organ = result.data.organ;
					_this.term = result.data.term;
					_this.good_at = result.data.good_at;
					_this.description = result.data.description;
					_this.honor = result.data.honor;
				})
			},
			getParam(path, name) {
				var reg = new RegExp("(^|\\?|&)" + name + "=([^&]*)(\\s|&|$)", "i");
				if (reg.test(path))
					return unescape(RegExp.$2.replace(/\+/g, " "));
				return "";
			}
		}
	}
</script>

<style>

</style>
