<style>
    @import "/static/mediate/css/law-list.css";
</style>
<template>
    <view>
        <scroll-view class="scrollview" @scrolltolower="bindDownLoad" scroll-y="true" :style="{height:phoneHeight}">
            <!-- <form class="orange-back" @submit="formSubmit">
                <view class="search-content">
                    <input type="text" name="keyword" class="search-input" placeholder="输入搜索内容" placeholder-style="color:white" :value="keyword" maxlength="30">
                    <button form-type="submit" plain="true" class="search-button">
                        <image class="search-button-glass" src="/static/mediate/images/g-001.png"></image>
                    </button>
                </view>
            </form> -->
            <view class="regulation-list">
                <template v-for="item in items" :key="item.id">
                    <view class="list" :data-id="item.id" @click="jump">
                        <view class="list-content">
                            <image class="list-image" src="/static/mediate/images/g-002.png"></image>
                            <view class="list-title">{{item.title}}</view>
                        </view>
                    </view>
                </template>
            </view>
        </scroll-view>
        <view class="popup-window-foot"></view>
    </view>
</template>

<script>
    import myFoot from "./footer.vue"

    export default {
        components: {
            myFoot
        },
        data() {
            return {
                keyword: '',
                items: [],
                page: 1,
                last_page: 0,
                phoneHeight: 0,
            }
        },
        onLoad(option) {
            this.option = option;
        },
        onShow() {
            this.getlist(true, 1);
            this.getHeight();
        },
        methods: {
            getlist(is_super, page) {
                var _this = this,
                    keyword = _this.option.keyword || '';
                this.$http.get('mediate/article', {
                    category_id: 0,
                    page: page,
                    keyword: _this.keyword ? _this.keyword : keyword
                }, result => {
                    _this.page = result.data.current_page;
                    _this.last_page = result.data.last_page;
                    _this.itemsdata = result.data.data;
                    if (is_super === true) {
                        _this.items = result.data.data;
                        _this.page = 1;
                    } else {
                        _this.items = _this.items.concat(result.data.data)
                    }
                })
            },
            getParam(path, name) {
                var reg = new RegExp("(^|\\?|&)" + name + "=([^&]*)(\\s|&|$)", "i");
                if (reg.test(path))
                    return unescape(RegExp.$2.replace(/\+/g, " "));
                return "";
            },
            bindDownLoad: function() {
                var _this = this;
                // 已经是最后一页
                if (_this.page >= _this.last_page) {
                    _this.no_more = true
                    return false;
                }
                _this.page += 1;
                _this.getlist(false, _this.page);
            },
            jump: function(e) {
                const id = e.currentTarget.dataset.id;
                uni.navigateTo({
                    url: '/pages/mediate/law-detail?id=' + id,
                });
            },
            formSubmit: function(e) {
                var keyword = e.detail.value.keyword;
                this.keyword = keyword;
                if (keyword) {
                    this.getlist(true);
                } else {
                    uni.navigateTo({
                        url: '/pages/mediate/law-list',
                    });
                }
            },
            getHeight: function(e) {
                var _this = this;
                uni.getSystemInfo({
                    success: function(res) {
                        if (res.safeAreaInsets.bottom) {
                            _this.phoneHeight = res.windowHeight - res.safeAreaInsets.bottom + "px";
                        } else {
                            _this.phoneHeight = res.screenHeight + "px";
                        }
                    },
                });
            }
        }
    }
</script>

<style>

</style>
