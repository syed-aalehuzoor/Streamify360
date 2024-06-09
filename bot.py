from core import *

app = ApplicationBuilder().token(bot_token).build()
app.job_queue

async def start(update: Update, context:ContextTypes.DEFAULT_TYPE):
    user_plans.add_user(userid=str(update.effective_user.id),username=update.effective_user.full_name)
    await context.bot.send_message(chat_id=update.effective_chat.id, text='Welcome to Transcoder')
    await bot_map.main_menu(update=update,context=context)

app.add_handler(plan_menu_handler)
app.add_handler(video_inputs_handler)
app.add_handler(CommandHandler("start",start))
app.add_handler(MessageHandler(filters.ALL & ~filters.COMMAND, bot_map.main_menu))
app.add_handler(CallbackQueryHandler(bot_map.main_menu))
app.run_polling()
