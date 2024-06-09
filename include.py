from telegram.ext import (
    ApplicationBuilder,
    CommandHandler,
    ContextTypes,
    filters,
    MessageHandler,
    CallbackQueryHandler,
    CallbackContext,
    ConversationHandler,
    JobQueue
    )

from telegram import (
    Update,
    InlineKeyboardButton,
    InlineKeyboardMarkup,
    ReplyKeyboardMarkup,
    KeyboardButton,
    Bot,
    )

from database import user_plans, plans, api_hash, api_id, bot_token 

import datetime
import os
import subprocess
import re
import sys
import asyncio

from telethon import TelegramClient

UPLOADING_VIDEO, UPLOADING_LOGO, UPLOADING_SUBTITLE, CURRENT_PLAN = range(4)