<style>
    @import "/static/mediate/css/apply-index.css";
</style>
<template>
    <view>
        <view class="orange-back orange-back-apply">
            <form class="orange-back-form" @submit="submit">
                <view class="form-text">调解人信息</view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">姓名:</text>
                        <input class="uni-input" :value="username" placeholder="" maxlength="15" disabled />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">电话:</text>
                        <input class="uni-input" :value="mobile" placeholder="" maxlength="15" disabled />
                    </view>
                    <view class="form-note-black">*以上信息我们将在系统中严格保密</view>
                </view>
                <view class="form-text">纠纷描述</view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">纠纷类型:</text>
                        <picker name="category_id" @change="bindPickerChange" :value="index" :range="category" range-key="name">
                            <view class="uni-input">{{category_name}}</view>
                        </picker>
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">发生时间:</text>
                        <picker name="occurred_time" mode="date" :value="date" :start="startDate" :end="endDate" @change="bindDateChange">
                            <view class="uni-input">{{date}}</view>
                        </picker>
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">发生地点:</text>
                        <textarea class="form-input-textarea" name="occurred_site" @input="inputoccurred_site" placeholder-style="color:#4F4F4F" placeholder="请填写" auto-height />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">过程描述:</text>
                        <textarea class="form-input-textarea" name="description" @input="inputdescription" placeholder-style="color:#4F4F4F" placeholder="请填写" auto-height />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">期望结果:</text>
                        <textarea class="form-input-textarea" name="outcome" @input="inputoutcome" placeholder-style="color:#4F4F4F" placeholder="请填写" auto-height />
                    </view>
                    <view class="form-input form-upload">
                        <text class="form-input-text">资料上传:</text>
                        <view class="form-upload-img">
                            <uni-file-picker class="choosePicture" limit="4" file-mediatype="all" mode="grid" @select="select" @delete="deletefile">
                            </uni-file-picker>
                        </view>
                    </view>
                </view>
                <button form-type="submit" class="form-button" :disabled="isDisable" v-if="canSubmit">确认申请</button>
                <button class="form-button form-button-light" v-else>确认申请</button>
                <view class="form-agreement">
                    <view>
                        <checkbox-group @change="checkboxChange">
                            <label>
                                <checkbox value="1" color="#ec6618" :checked="checkbox_value" />
                            </label>
                        </checkbox-group>
                    </view>
                    <view class="agreement-content">
                        <text>我已阅读并同意</text>
                        <text class="agreement-content-tip" @click="openWindow">《 人民调解当事人权利义务告知书》</text>
                    </view>
                </view>
            </form>
        </view>
        <view class="status-success" :class="specClass">
            <view class="success-tip">
                <view class="success-tip-img">
                    <image class="success-tip-image" src="/static/mediate/images/c-012.png" mode=""></image>
                </view>
                <view class="success-tip-title">申请成功</view>
                <view class="success-tip-content">你已成功申请调解，我们将会尽快开展工作 请及时留意调解最新动态</view>
                <view class="success-tip-phone">
                    <image class="success-tip-phone-image" src="/static/mediate/images/c-013.png"></image>
                    <view class="success-tip-phone-number">服务电话：{{site_tel}}</view>
                </view>
                <button class="success-tip-button" @click="close_tip">确定</button>
            </view>
        </view>
        <view class="popup-window">
            <uni-popup ref="popup" background-color="#fff" :mask-click="false">
                <view class="popup-content">
                    <view class="text"></view>
                    <scroll-view class="text-content" scroll-y="true">
                        <view v-html="informed" class="content"></view>
                    </scroll-view>
                    <view class="popup-buttons">
                        <view class="popup-button" @click="cancel">取消</view>
                        <view class="popup-button" @click="agree">同意</view>
                    </view>
                </view>
            </uni-popup>
        </view>
        <view class="popup-window-foot"></view>
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
                username: '',
                mobile: '',

                occurred_site: '',
                description: '',
                outcome: '',

                object_news: [],
                specClasstwo: 'form-button-light',
                specClass: 'hide',
                array: [],
                category: [],
                category_name: '',
                informed: '',
                site_tel: '',
                index: 0,
                checkbox_value: !1,
                date: '',
                imageStyles: {
                    width: 64,
                    height: 64,
                    border: {
                        radius: '50%'
                    }
                },
                listStyles: {
                    border: true, // 是否显示边框					
                    dividline: true, // 是否显示分隔线		
                    borderStyle: { // 线条样式
                        width: 1,
                        color: 'blue',
                        style: 'dashed',
                        radius: 2
                    }
                },
                amplification_image: '',
                amplification_open: 'hide',

                isDisable: !1, // 按钮disabled状态
                uploadFileArr: {}, // 文件上传状态数组
                uploadLoad: !1, // 文件总上传状态
            }
        },
        created() {
            this.$nextTick(() => {
                this.openWindow();
            })
            this.date = this.getDate({
                format: true
            })
        },
        onShow() {
            this.getdetail();
        },
        computed: {
            startDate() {
                return this.getDate('start');
            },
            endDate() {
                return this.getDate('end');
            },
            canSubmit() {
                return this.occurred_site == '' || this.description == '' || this.outcome == '' || this.checkbox_value ==
                    0 || this.uploadLoad ? false : true;
            }
        },
        watch: {
            uploadFileArr: {
                handler: function(newName, oldName) {
                    let arr = Object.values(newName)
                    this.uploadLoad = arr.indexOf('load') > -1;
                },
                deep: true
            }
        },
        methods: {
            inputoccurred_site(e) {
                if (e) {
                    this.occurred_site = e.detail.value;
                }
            },
            inputdescription(e) {
                if (e) {
                    this.description = e.detail.value;
                }
            },
            inputoutcome(e) {
                if (e) {
                    this.outcome = e.detail.value;
                }
            },
            getdetail() {
                var _this = this;
                _this.$http.get('mediate/user', {

                }, result => {
                    _this.username = result.data.username;
                    _this.mobile = result.data.mobile;
                    _this.$http.get('mediate/apply', {

                    }, result => {
                        _this.informed = result.data.informed;
                        _this.site_tel = result.data.site_tel;
                        _this.category = result.data.category;
                        _this.category_name = _this.category[_this.index]['name'];
                    });
                })
            },
            bindPickerChange: function(e) {
                this.index = e.detail.value;
                this.category_name = this.category[this.index]['name'];
            },
            bindDateChange: function(e) {
                this.date = e.detail.value
            },
            getDate(type) {
                const date = new Date();
                let year = date.getFullYear();
                let month = date.getMonth() + 1;
                let day = date.getDate();

                if (type === 'start') {
                    year = year - 20;
                } else if (type === 'end') {
                    year = year + 2;
                }
                month = month > 9 ? month : '0' + month;
                day = day > 9 ? day : '0' + day;
                return `${year}-${month}-${day}`;
            },
            showMsg: function(msg) {
                uni.showToast({
                    title: msg,
                    icon: 'error',
                    duration: 2000
                });
            },
            submit: function(e) {
                var that = this;
                if ((this.occurred_site) && (this.description) && (this.outcome) && (this.checkbox_value == 1)) {
                    const file_all = this.object_news;
                    var files = "";
                    for (let a in file_all) {
                        if (!files) {
                            files = files + file_all[a];
                        } else {
                            files = files + "|" + file_all[a];
                        }
                    }
                    if (this.checkbox_value) {
                        const formDetail = e.detail.value;
                        const category_id = formDetail.category_id;
                        const occurred_time = formDetail.occurred_time;
                        const occurred_site = formDetail.occurred_site;
                        const description = formDetail.description;
                        const outcome = formDetail.outcome;
                        if (!occurred_site) {
                            this.showMsg('请填写发生地点');
                            return;
                        }
                        if (!description) {
                            this.showMsg('请填写过程描述');
                            return;
                        }
                        if (!outcome) {
                            this.showMsg('请填写期望结果');
                            return;
                        }
                        that.isDisable = !0;
                        this.$http.post('mediate/apply_save', {
                            category_id: this.category[category_id]['id'],
                            occurred_time: formDetail.occurred_time,
                            occurred_site: formDetail.occurred_site,
                            description: formDetail.description,
                            outcome: formDetail.outcome,
                            attach_file: files,
                        }, result => {
                            if (result.code == 1) {
                                this.specClass = "show";
                            }
                        }, res => {}, res => {
                            that.isDisable = !1;
                        })
                    } else {
                        uni.showToast({
                            title: '请勾选以下协议',
                            icon: 'error',
                            duration: 2000
                        });
                    }
                }
            },
            close_tip: function(e) {
                uni.redirectTo({
                    url: '/pages/mediate/personal-index',
                });
            },
            openWindow() {
                this.$refs.popup.open('center');
            },
            checkboxChange: function(e) {
                this.checkbox_value = !this.checkbox_value
            },
            agree() {
                this.checkbox_value = !0;
                this.$refs.popup.close();
            },
            cancel() {
                this.checkbox_value = !1;
                this.$refs.popup.close();
            },
            // 获取上传状态 (上传照片、视频、PDF资料)
            select(e) {
                let _this = this;
                let obj = {};
                let object_news = this.object_news;
                e.tempFilePaths.forEach((val, ind) => {
                    _this.uploadFileArr[e.tempFiles[ind].uuid] = 'load';
                    this.$http.upload('index/upload', val, {}, res => {
                        obj[e.tempFiles[ind].uuid] = res.data.url; //上传文件位置
                        this.object_news = Object.assign(obj, object_news);
                        _this.uploadFileArr[e.tempFiles[ind].uuid] = 'success';
                    })
                })
            },
            //文件从列表移除时触发
            deletefile(e) {
                const i = e.tempFile.uuid;
                const object_all = this.object_news;
                Reflect.deleteProperty(object_all, i)
                this.object_news = object_all;
            },
            //隐藏名称
            hideName(name) {
                let newStr;
                if (name.length === 2) {
                    newStr = name.substr(0, 1) + '*';
                } else if (name.length > 2) {
                    let char = '';
                    for (let i = 0, len = name.length - 2; i < len; i++) {
                        char += '*';
                    }
                    newStr = name.substr(0, 1) + char + name.substr(-1, 1);
                } else {
                    newStr = name;
                }
                return newStr;
            }
        }
    }
</script>
