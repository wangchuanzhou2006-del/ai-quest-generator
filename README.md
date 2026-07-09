# AI Quest Generator

一个基于 Laravel + OpenAI API 的 AI 游戏任务生成平台，帮助游戏开发者快速生成任务、NPC 对话和游戏剧情，提高游戏内容制作效率。

## 功能规划

- AI 生成游戏任务
- AI 生成 NPC 对话
- AI 生成游戏剧情
- 玩家任务管理
- 历史记录保存
- 用户登录
- 后台管理

## 技术栈

- Laravel 8
- PHP 7.3+
- MySQL
- Bootstrap 或 Vue
- OpenAI API

## 开发计划

- [x] 初始化 Laravel 项目
- [ ] 配置数据库连接
- [ ] 用户登录
- [ ] AI 任务生成
- [ ] NPC 对话生成
- [ ] 剧情生成
- [ ] 历史记录
- [ ] 后台管理

## 本地开发

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

访问：`http://127.0.0.1:8000`

## 环境变量

在 `.env` 中配置：

```env
OPENAI_API_KEY=your_openai_api_key
DB_DATABASE=ai_quest_generator
DB_USERNAME=root
DB_PASSWORD=
```

## 作者

Wang Chuanzhou
