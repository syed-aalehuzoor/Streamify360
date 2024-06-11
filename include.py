from telegram.ext import (
    ApplicationBuilder,
    CommandHandler,
    ContextTypes,
    filters,
    MessageHandler,
    CallbackQueryHandler,
    CallbackContext,
    ConversationHandler,
    JobQueue,
    PreCheckoutQueryHandler,
    )

from telegram import (
    Update,
    InlineKeyboardButton,
    InlineKeyboardMarkup,
    ReplyKeyboardMarkup,
    KeyboardButton,
    Bot,
    LabeledPrice
    )

from database import user_plans, plans, api_hash, api_id, bot_token 

import datetime
import os
import subprocess
import re
import sys
import asyncio

from typing import List

from telethon import TelegramClient

UPLOADING_VIDEO, UPLOADING_LOGO, UPLOADING_SUBTITLE, USER_PLAN_SUBSCRIPTION, SHOWING_PLAN, INVOICE_SENT, WAITING_FOR_PAYMENT = range(7)
