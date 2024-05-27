from core import *
app = ApplicationBuilder().token("7189345969:AAH6NnFud_Ey6DfDUsi0w_MReJ-jE0wibuU").build()

async def start(update: Update, context:ContextTypes.DEFAULT_TYPE):
    await context.bot.send_message(chat_id=update.effective_chat.id, text='Welcome to Transcoder')

app.add_handler(plan_menu_handler)
app.add_handler(video_inputs_handler)
app.add_handler(CommandHandler("start",start))
app.run_polling()